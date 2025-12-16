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
    idCard INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUser) REFERENCES users(idUser) ON DELETE CASCADE,
    FOREIGN KEY (idCard) REFERENCES cards(idCard) ON DELETE CASCADE
);

-- Create expenses table
CREATE TABLE IF NOT EXISTS expenses (
    idEx INT PRIMARY KEY AUTO_INCREMENT,
    amountEx DECIMAL(10,2) NOT NULL,
    dateEx DATE NOT NULL,
    descriptionEx VARCHAR(250) DEFAULT 'Unknown',
    idUser INT NOT NULL,
    idCard INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUser) REFERENCES users(idUser) ON DELETE CASCADE,
    FOREIGN KEY (idCard) REFERENCES cards(idCard) ON DELETE CASCADE
);

--Create cards table
CREATE TABLE IF NOT EXISTS cards(
    idCard INT PRIMARY KEY AUTO_INCREMENT,
    idUser INT NOT NULL,
    cardName VARCHAR(50) NOT NULL,
    bankName VARCHAR(50) NOT NULL,
    cardNumber VARCHAR(20),
    isMain BOOLEAN DEFAULT FALSE,
    currentBalance DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUser) REFERENCES users(idUser) ON DELETE CASCADE
);