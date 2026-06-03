-- Deckoid ERP System Database Schema

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

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

-- Indexes for users
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_status ON users(status);

-- Insert default admin user (username: admin, password: admin123)
-- Password stored using SHA2; update application hashing if necessary.
INSERT IGNORE INTO users (id, full_name, email, username, password_hash, role, status, created_at, updated_at)
VALUES (UUID(), 'Administrator', 'admin@example.com', 'admin', SHA2('admin123',256), 'admin', 'active', NOW(), NOW());

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
    company VARCHAR(255) NOT NULL,
    contact_person VARCHAR(150) NOT NULL,
    mobile_number VARCHAR(20) NOT NULL,
    email_id VARCHAR(255) NULL,
    city VARCHAR(100) NULL,
    state VARCHAR(100) NULL,
    lead_category ENUM('Hot','Warm','Cold') NOT NULL,
    lead_status VARCHAR(100) NOT NULL,
    assigned_to CHAR(36) NULL,
    next_followup_date DATE NULL,
    estimated_budget DECIMAL(12,2) NULL,
    payment_status VARCHAR(100) NOT NULL,
    reference_by VARCHAR(255) NULL,
    remarks TEXT NULL,
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
CREATE INDEX idx_leads_city ON leads(city);
CREATE INDEX idx_leads_payment_status ON leads(payment_status);
CREATE INDEX idx_leads_assigned_to ON leads(assigned_to);
CREATE INDEX idx_leads_created_by ON leads(created_by);

-- Create lead_activity_logs table
CREATE TABLE IF NOT EXISTS lead_activity_logs (
    id CHAR(36) PRIMARY KEY,
    lead_id CHAR(36) NOT NULL,
    user_id CHAR(36) NULL,
    company VARCHAR(255) NULL,
    activity_type VARCHAR(100) NOT NULL,
    old_value TEXT NULL,
    new_value TEXT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Create followups table
CREATE TABLE IF NOT EXISTS followups (
    id CHAR(36) PRIMARY KEY NOT NULL,
    lead_id CHAR(36) NOT NULL,
    followup_date DATE NOT NULL,
    remarks TEXT NULL,
    status ENUM('Active', 'Completed', 'Lost') DEFAULT 'Active',
    created_by CHAR(36) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Create customer_onboarding table
CREATE TABLE IF NOT EXISTS customer_onboarding (
    id CHAR(36) PRIMARY KEY NOT NULL,
    lead_id CHAR(36) NOT NULL,
    project_name VARCHAR(255) NOT NULL,
    company VARCHAR(255) NULL,
    contact_person VARCHAR(150) NULL,
    add_work VARCHAR(255) NULL,
    status ENUM('Pending', 'In Progress', 'On Hold', 'Completed') DEFAULT 'Pending',
    remarks TEXT NULL,
    onboarding_date DATE NOT NULL,
    created_by CHAR(36) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
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
    payment_status VARCHAR(20) DEFAULT 'Pending',
    created_by CHAR(36) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
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
CREATE INDEX idx_invoices_created_by ON invoices(created_by);

-- Create projects table
CREATE TABLE IF NOT EXISTS projects (
    id CHAR(36) PRIMARY KEY NOT NULL,
    lead_id CHAR(36) NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    status ENUM('Planning', 'Active', 'On Hold', 'Completed', 'Cancelled') DEFAULT 'Planning',
    start_date DATE NULL,
    end_date DATE NULL,
    budget DECIMAL(12,2) DEFAULT 0.00,
    created_by CHAR(36) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Create tasks table
CREATE TABLE IF NOT EXISTS tasks (
    id CHAR(36) PRIMARY KEY NOT NULL,
    project_id CHAR(36) NULL,
    assigned_to CHAR(36) NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    priority ENUM('Low', 'Medium', 'High', 'Urgent') DEFAULT 'Medium',
    status ENUM('Todo', 'In Progress', 'Review', 'Done') DEFAULT 'Todo',
    due_date DATETIME NULL,
    created_by CHAR(36) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Create leave_requests table
CREATE TABLE IF NOT EXISTS leave_requests (
    id CHAR(36) PRIMARY KEY NOT NULL,
    user_id CHAR(36) NOT NULL,
    leave_type VARCHAR(100) NOT NULL,
    reason TEXT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    approved_by CHAR(36) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Create kpis table
CREATE TABLE IF NOT EXISTS kpis (
    id CHAR(36) PRIMARY KEY NOT NULL,
    user_id CHAR(36) NOT NULL,
    metric_name VARCHAR(255) NOT NULL,
    target_value DECIMAL(12,2) NOT NULL,
    actual_value DECIMAL(12,2) DEFAULT 0.00,
    period_month TINYINT NOT NULL,
    period_year SMALLINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id CHAR(36) PRIMARY KEY NOT NULL,
    user_id CHAR(36) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255) NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Additional Indexes
CREATE INDEX idx_projects_status ON projects(status);
CREATE INDEX idx_tasks_status ON tasks(status);
CREATE INDEX idx_tasks_assigned ON tasks(assigned_to);
CREATE INDEX idx_leaves_status ON leave_requests(status);
CREATE INDEX idx_notifications_user_unread ON notifications(user_id, is_read);

COMMIT;