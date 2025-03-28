document.addEventListener('DOMContentLoaded', function() {
    // Initialize Google Sign-In
    function initializeGoogleSignIn() {
        try {
            google.accounts.id.initialize({
                client_id: '511726290722-40jvb5tcng5af99i1thf6gbgado4nae6.apps.googleusercontent.com',
                callback: handleGoogleSignIn,
                auto_select: false,
                cancel_on_tap_outside: true,
                context: 'signin',
                ux_mode: 'popup',
                error_callback: function(error) {
                    console.error('Google Sign-In Error:', error);
                    alert('Failed to initialize Google Sign-In: ' + error.message);
                }
            });
            
            // Render the Google Sign-In button
            google.accounts.id.renderButton(
                document.getElementById('google-signin-btn'), 
                { 
                    theme: 'outline', 
                    size: 'large',
                    width: '100%',
                    text: 'signin_with',
                    shape: 'rectangular',
                    logo_alignment: 'center'
                }
            );
            
            console.log('Google Sign-In initialized successfully');
        } catch (error) {
            console.error('Error initializing Google Sign-In:', error);
            alert('Error initializing Google Sign-In: ' + error.message);
        }
    }
    
    // Handle Google Sign-In response
    function handleGoogleSignIn(response) {
        console.log('Google Sign-In response received', response);
        
        // Show loading state
        const googleButton = document.getElementById('google-signin-btn');
        if (googleButton) {
            googleButton.innerHTML = '<span>Processing login...</span>';
        }
        
        // The response contains a JWT ID token
        const idToken = response.credential;
        if (!idToken) {
            console.error('No credential in response');
            alert('Login failed: No credential received from Google');
            resetButton();
            return;
        }
        
        // Send the token to your server for validation and authentication
        fetch('/login/google', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ credential: idToken })
        })
        .then(response => {
            console.log('Server response status:', response.status);
            
            // Check if response is OK
            if (!response.ok) {
                console.error('Server returned error status:', response.status);
            }
            
            // Try to parse JSON even if response status is not OK
            return response.text().then(text => {
                try {
                    return text ? JSON.parse(text) : {};
                } catch (e) {
                    console.error('Failed to parse response as JSON:', e);
                    console.log('Raw response:', text);
                    throw new Error('Server returned invalid JSON: ' + text.substring(0, 100) + '...');
                }
            });
        })
        .then(data => {
            console.log('Login response data:', data);
            
            if (data.success) {
                console.log('Login successful, redirecting to:', data.redirect);
                
                // Handle redirect - ensure we have a valid URL
                const redirectUrl = data.redirect;
                if (redirectUrl) {
                    // Force page reload and redirect
                    window.location.href = redirectUrl;
                } else {
                    console.error('No redirect URL provided');
                    window.location.href = '/dashboard'; // Fallback
                }
            } else {
                // Handle error
                const errorMsg = data.error || 'Unknown error';
                console.error('Google sign-in error:', errorMsg);
                alert('Google sign-in failed: ' + errorMsg);
                resetButton();
            }
        })
        .catch(error => {
            console.error('Error during Google sign-in:', error);
            alert('Error during Google sign-in: ' + error.message);
            resetButton();
        });
    }
    
    // Helper function to reset the Google button
    function resetButton() {
        const googleButton = document.getElementById('google-signin-btn');
        if (googleButton) {
            googleButton.innerHTML = '';
            try {
                google.accounts.id.renderButton(googleButton, { 
                    theme: 'outline', 
                    size: 'large',
                    width: '100%',
                    text: 'signin_with'
                });
            } catch (e) {
                console.error('Failed to re-render Google button:', e);
                googleButton.innerHTML = '<div style="padding: 10px; text-align: center;">Sign in with Google</div>';
            }
        }
    }
    
    // Initialize Google Sign-In when the Google API is fully loaded
    if (typeof google !== 'undefined' && google.accounts) {
        initializeGoogleSignIn();
    } else {
        // If the Google API hasn't loaded yet, wait for it
        window.onGoogleLibraryLoad = initializeGoogleSignIn;
        console.log('Waiting for Google API to load...');
        
        // Add a fallback in case the Google API fails to load
        setTimeout(function() {
            if (typeof google === 'undefined' || !google.accounts) {
                console.error('Google API failed to load after timeout');
                const googleButton = document.getElementById('google-signin-btn');
                if (googleButton) {
                    googleButton.innerHTML = '<div style="padding: 10px; text-align: center; color: red;">Google Sign-In unavailable</div>';
                }
            }
        }, 10000); // 10 second timeout
    }
});