-- Create the database
CREATE DATABASE IF NOT EXISTS atlas;
USE atlas;

-- Create the Utilisateur table with roles for Voyageur, SupportClient, and Admin
CREATE TABLE IF NOT EXISTS Utilisateur (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50),
    surname VARCHAR(50),
    age INT,
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    adresse VARCHAR(255),
    role ENUM('Voyageur', 'SupportClient', 'Admin') NOT NULL,
    profileImage VARCHAR(255),
    
    -- Voyageur-specific fields
    num_telph VARCHAR(20),
    voyageurPreferences VARCHAR(255),
    destinations_preferrees TEXT,
    budget DOUBLE,
    reservationHistorique TEXT,
    completedTrips TEXT,
    
    -- SupportClient-specific fields
    assignedTickets TEXT,
    
    -- Admin-specific fields
    adminPrivileges TEXT
);
-- Create the AirLine table if it does not exist
CREATE TABLE IF NOT EXISTS AirLine (
    airline_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255),
    pays VARCHAR(255),
    logo VARCHAR(255)
    );

-- Créer la table Vol (Vol)
CREATE TABLE IF NOT EXISTS Vol (
    vol_id INT PRIMARY KEY AUTO_INCREMENT,
    departure VARCHAR(255) NOT NULL,
    destination VARCHAR(255) NOT NULL,
    departureDate DATE NOT NULL,
    returnDate DATE NOT NULL,
    availableSeats INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    airline_id INT, -- Clé étrangère pointant vers Airline
    FOREIGN KEY (airline_id) REFERENCES Airline(airline_id)
    );
CREATE TABLE IF NOT EXISTS Reservation (
                                           id INT PRIMARY KEY AUTO_INCREMENT,
                                           prenom VARCHAR(50) NOT NULL,
    nom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    typeChambre ENUM('single', 'double', 'suite') NOT NULL,
    nombrePersonnes INT NOT NULL,
    dateArrivee DATE NOT NULL,
    dateDepart DATE NOT NULL,
    nombreNuits INT NOT NULL,
    petitDejeuner BOOLEAN NOT NULL,
    litSupplementaire BOOLEAN NOT NULL,
    vueSpecifique ENUM('mer', 'piscine'),
    commentaire TEXT,
    montantTotal DECIMAL(10, 2) NOT NULL
    );
CREATE TABLE IF NOT EXISTS Forfait (
    packageld INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DOUBLE NOT NULL,
    duration INT NOT NULL,
    destinations TEXT,
    availableSeats INT NOT NULL,
    packageImage VARCHAR(255)
);

-- Create the FlightReservation table
CREATE TABLE IF NOT EXISTS FlightReservation (
                                                 reservation_id INT PRIMARY KEY AUTO_INCREMENT, -- Unique ID for the reservation
                                                 user_id INT NOT NULL, -- Foreign key to link to the Utilisateur table (Voyageur)
                                                 vol_id INT NOT NULL, -- Foreign key to link to the Vol table
                                                 passenger_name VARCHAR(100) NOT NULL, -- Name of the passenger
    passenger_email VARCHAR(100) NOT NULL, -- Email of the passenger
    passenger_phone VARCHAR(20) NOT NULL, -- Phone number of the passenger
    reservation_date DATETIME NOT NULL, -- Date and time of the reservation
    number_of_passengers INT NOT NULL, -- Number of passengers
    total_price DECIMAL(10, 2) NOT NULL, -- Total price of the reservation
    payment_status ENUM('Pending', 'Paid', 'Cancelled') NOT NULL DEFAULT 'Pending', -- Payment status
    reservation_status ENUM('Confirmed', 'Pending', 'Cancelled') NOT NULL DEFAULT 'Pending', -- Reservation status
    special_requests TEXT, -- Any special requests from the passenger
    FOREIGN KEY (user_id) REFERENCES Utilisateur(id), -- Link to the Utilisateur table
    FOREIGN KEY (vol_id) REFERENCES Vol(vol_id) -- Link to the Vol table
    );
