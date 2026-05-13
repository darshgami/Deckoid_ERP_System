-- Deckoid ERP System Database Schema
-- Optimized for XAMPP (MariaDB/MySQL)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS lead_management;
USE lead_management;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id CHAR(36) PRIMARY KEY NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','staff') DEFAULT 'staff',
    status ENUM('active','inactive') DEFAULT 'active',
    phone_number VARCHAR(20) NULL,
    bio TEXT NULL,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Indexes for users (using CREATE INDEX IF NOT EXISTS)
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_status ON users(status);

-- Create sessions table
CREATE TABLE IF NOT EXISTS sessions (
    id CHAR(36) PRIMARY KEY NOT NULL,
    user_id CHAR(36) NOT NULL,
    refresh_token VARCHAR(512) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(500) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Indexes for sessions
CREATE INDEX idx_sessions_user_id ON sessions(user_id);
CREATE INDEX idx_sessions_expires_at ON sessions(expires_at);

-- Create leads table
CREATE TABLE IF NOT EXISTS leads (
    id CHAR(36) PRIMARY KEY NOT NULL,
    lead_id VARCHAR(50) UNIQUE NOT NULL,
    lead_date DATE NOT NULL,
    company_client_name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(150) NOT NULL,
    mobile_number VARCHAR(20) NOT NULL,
    alternative_number VARCHAR(20) NULL,
    email_id VARCHAR(255) NULL,
    city VARCHAR(100) NULL,
    state VARCHAR(100) NULL,
    country VARCHAR(100) DEFAULT 'India',
    zip_code VARCHAR(20) NULL,
    source_of_lead VARCHAR(100) NOT NULL,
    service_interested_in VARCHAR(255) NULL,
    lead_category ENUM('Hot','Warm','Cold') NOT NULL,
    lead_status VARCHAR(100) NOT NULL,
    priority ENUM('High','Medium','Low') DEFAULT 'Medium',
    assigned_to CHAR(36) NULL,
    next_followup_date DATE NULL,
    last_followup_notes TEXT NULL,
    requirement_details TEXT NULL,
    estimated_budget DECIMAL(12,2) NULL,
    proposal_sent BOOLEAN DEFAULT FALSE,
    meeting_scheduled BOOLEAN DEFAULT FALSE,
    quotation_sent BOOLEAN DEFAULT FALSE,
    deal_status VARCHAR(100) NOT NULL,
    expected_closing_date DATE NULL,
    payment_status VARCHAR(100) NOT NULL,
    client_onboard_date DATE NULL,
    project_start_date DATE NULL,
    project_status VARCHAR(100) NULL,
    reference_by VARCHAR(255) NULL,
    website_social_link VARCHAR(500) NULL,
    remarks_notes TEXT NULL,
    created_by CHAR(36) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Indexes for leads
CREATE INDEX idx_leads_mobile_number ON leads(mobile_number);
CREATE INDEX idx_leads_email_id ON leads(email_id);
CREATE INDEX idx_leads_lead_status ON leads(lead_status);
CREATE INDEX idx_leads_priority ON leads(priority);
CREATE INDEX idx_leads_city ON leads(city);
CREATE INDEX idx_leads_deal_status ON leads(deal_status);
CREATE INDEX idx_leads_payment_status ON leads(payment_status);
CREATE INDEX idx_leads_assigned_to ON leads(assigned_to);

-- Create lead_activity_logs table
CREATE TABLE IF NOT EXISTS lead_activity_logs (
    id CHAR(36) PRIMARY KEY,
    lead_id CHAR(36) NOT NULL,
    user_id CHAR(36) NULL,
    company_client_name VARCHAR(255) NULL,
    activity_type VARCHAR(100) NOT NULL,
    old_value TEXT NULL,
    new_value TEXT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Create invoices table
CREATE TABLE IF NOT EXISTS invoices (
    id CHAR(36) PRIMARY KEY NOT NULL,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    invoice_date DATE NOT NULL,
    invoice_type ENUM('With GST', 'Without GST') NOT NULL,
    party_name VARCHAR(255) NOT NULL,
    mobile_number VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    gstin VARCHAR(15) NULL,
    place_of_supply VARCHAR(100) NULL,
    sub_total DECIMAL(12,2) NOT NULL,
    gst_total DECIMAL(12,2) DEFAULT 0.00,
    grand_total DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create invoice_items table
CREATE TABLE IF NOT EXISTS invoice_items (
    id CHAR(36) PRIMARY KEY NOT NULL,
    invoice_id CHAR(36) NOT NULL,
    service_name VARCHAR(255) NOT NULL,
    hsn_sac VARCHAR(20) NULL,
    qty DECIMAL(12,3) NOT NULL,
    rate DECIMAL(12,2) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
);

-- Indexes for invoices
CREATE INDEX idx_invoices_number ON invoices(invoice_number);
CREATE INDEX idx_invoices_date ON invoices(invoice_date);
CREATE INDEX idx_invoices_party ON invoices(party_name);

COMMIT;