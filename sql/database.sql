-- Create database
CREATE DATABASE IF NOT EXISTS smart_wallet_Av;
USE smart_wallet_Av;

-- Create users table
CREATE TABLE IF NOT EXISTS users(
    idUser INT PRIMARY KEY AUTO_INCREMENT,
    fullName VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create incomes table
CREATE TABLE IF NOT EXISTS incomes (
    idIn INT PRIMARY KEY AUTO_INCREMENT,
    amountIn DECIMAL(10,2) NOT NULL,
    dateIn DATE NOT NULL,
    descriptionIn VARCHAR(250) DEFAULT 'Unknown',
    idUser INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUser) REFERENCES users(idUser) ON DELETE CASCADE
);

-- Create expenses table
CREATE TABLE IF NOT EXISTS expenses (
    idEx INT PRIMARY KEY AUTO_INCREMENT,
    amountEx DECIMAL(10,2) NOT NULL,
    dateEx DATE NOT NULL,
    descriptionEx VARCHAR(250) DEFAULT 'Unknown',
    idUser INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUser) REFERENCES users(idUser) ON DELETE CASCADE
);

INSERT INTO incomes (amountIn, dateIn, descriptionIn) VALUES 
(3000.00, '2024-01-15', 'Salary'),
(500.00, '2024-01-20', 'Freelance Work');

INSERT INTO expenses (amountEx, dateEx, descriptionEx) VALUES 
(800.00, '2024-01-05', 'Rent'),
(200.00, '2024-01-10', 'Groceries'),
(50.00, '2024-01-12', 'Utilities');

