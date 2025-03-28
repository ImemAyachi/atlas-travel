<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Form\UserType;
use App\Form\LoginType;
use Symfony\Component\Form\FormError;

class AuthenticationController extends AbstractController
{
    private $entityManager;
    private $tokenStorage;
    
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }
    
    #[Route('/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        $authError = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        
        $form = $this->createForm(LoginType::class, [
            'email' => $lastUsername,
        ]);
        
        $form->handleRequest($request);
        
        $fieldErrors = [];
        
        if ($authError) {
            switch ($authError->getMessageKey()) {
                case 'Invalid credentials.':
                    $fieldErrors['email'] = 'Invalid email or password';
                    $fieldErrors['password'] = 'Invalid email or password';
                    break;
                case 'User account is disabled.':
                    $fieldErrors['email'] = 'Your account is disabled';
                    break;
                default:
                    $fieldErrors['email'] = 'Authentication error: ' . $authError->getMessageKey();
                    break;
            }
        }

        return $this->render('authentication/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => null,
            'form' => $form->createView(),
            'fieldErrors' => $fieldErrors,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    
    #[Route('/login/google', name: 'google_login', methods: ['POST'])]
    public function googleLogin(Request $request, Security $security): JsonResponse
    {
        $response = new JsonResponse();
        
        try {
            $data = json_decode($request->getContent(), true);
            $idToken = $data['credential'] ?? null;
            
            if (!$idToken) {
                return new JsonResponse(['success' => false, 'error' => 'No credential provided'], 400);
            }
            
            $tokenParts = explode('.', $idToken);
            if (count($tokenParts) !== 3) {
                return new JsonResponse(['success' => false, 'error' => 'Invalid token format'], 400);
            }
            
            $payloadBase64 = $tokenParts[1];
            $payloadBase64 = str_replace(['-', '_'], ['+', '/'], $payloadBase64);
            $payloadBase64 = str_pad($payloadBase64, strlen($payloadBase64) % 4, '=', STR_PAD_RIGHT);
            
            $payload = json_decode(base64_decode($payloadBase64), true);
            if (!$payload) {
                return new JsonResponse(['success' => false, 'error' => 'Failed to decode token payload'], 400);
            }
            
            $email = $payload['email'] ?? null;
            $name = $payload['name'] ?? null;
            $googleId = $payload['sub'] ?? null;
            $picture = $payload['picture'] ?? null;
            
            if (!$email || !$googleId) {
                return new JsonResponse(['success' => false, 'error' => 'Missing required fields in token payload'], 400);
            }
            
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            
            if (!$user) {
                $user = new User();
                $user->setEmail($email);
                
                $nameParts = explode(' ', $name);
                $firstName = $nameParts[0] ?? 'Google';
                $lastName = count($nameParts) > 1 ? end($nameParts) : 'User';
                
                $user->setName($name ?? 'Google User');
                $user->setNom($lastName);
                $user->setSurname($lastName);
                $user->setPrenom($firstName);
                $user->setRole('tourist');
                $user->setAdresse('Please update your address');
                $user->setAge(0);
                
                if ($picture) {
                    $user->setProfileImage($picture);
                }
                
                $user->setPassword(password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT));
                
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
            
            if (empty($user->getRole())) {
                $user->setRole('tourist');
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
            
            $firewallName = 'main';
            $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
            $this->tokenStorage->setToken($token);
            
            $session = $request->getSession();
            if (!$session->isStarted()) {
                $session->start();
            }
            $session->set('_security_'.$firewallName, serialize($token));
            
            $session->set('google_user_data', [
                'name' => $name,
                'email' => $email,
                'picture' => $picture,
                'google_id' => $googleId
            ]);
            
            $redirectUrl = $this->generateUrl('app_dashboard', [], 0);
            
            return new JsonResponse([
                'success' => true,
                'message' => 'Authentication successful',
                'email' => $email,
                'role' => $user->getRole(),
                'userId' => $user->getId(),
                'redirect' => $redirectUrl
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false, 
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $form->get('email')->getData()]);
                
                if ($existingUser) {
                    $form->get('email')->addError(new \Symfony\Component\Form\FormError('This email is already registered.'));
                    return $this->render('authentication/register.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }
                
                $user->setPassword(password_hash($form->get('password')->getData(), PASSWORD_BCRYPT));
                
                $firstname = $form->get('firstname')->getData();
                $lastname = $form->get('lastname')->getData();
                
                $user->setName($firstname.' '.$lastname);
                $user->setPrenom($firstname);
                $user->setNom($lastname);
                $user->setSurname($firstname);
                
                $user->setRole('tourist');
                
                if ($form->has('phone') && $form->get('phone')->getData()) {
                    $user->setNumTelph($form->get('phone')->getData());
                }
                
                $profileImage = $form->get('profile_image')->getData();
                if ($profileImage) {
                    $originalFilename = pathinfo($profileImage->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = preg_replace('/[^A-Za-z0-9_]/', '', $originalFilename);
                    $safeFilename = strtolower($safeFilename);
                    if (empty($safeFilename)) {
                        $safeFilename = 'profile';
                    }
                    $originalExtension = $profileImage->getClientOriginalExtension();
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$originalExtension;
                    
                    try {
                        $profileImage->move(
                            $this->getParameter('profile_images_directory'),
                            $newFilename
                        );
                        $user->setProfileImage($newFilename);
                    } catch (FileException $e) {
                    }
                }
                
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                
                $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
                $this->tokenStorage->setToken($token);
                
                return $this->redirectToRoute('app_dashboard');
            }
        }
        
        return $this->render('authentication/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
} 