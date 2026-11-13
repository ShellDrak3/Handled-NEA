-- Store registered accounts for managers and customers.
-- Passwords are stored as hashes, not plain text.
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('manager', 'customer') NOT NULL
);

-- Store customer contact information independently of properties.
-- A customer can have multiple properties.
-- user_id is nullable so customers can exist without an online account.
CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(30),
    notes TEXT,

    -- If an account is deleted, retain the customer record.
    FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE SET NULL
);

-- Store individual properties belonging to each customer.
-- Properties can contain additional access information in their notes.
CREATE TABLE properties (
    property_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    address VARCHAR(255) NOT NULL,
    notes TEXT,

    -- Delete a property automatically if its customer is deleted.
    FOREIGN KEY (customer_id)
        REFERENCES customers(customer_id)
        ON DELETE CASCADE
);

-- Store services that the business offers.
-- Each service has a starting price and estimated duration.
CREATE TABLE services (
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    description TEXT,
    base_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    base_duration INT NOT NULL DEFAULT 0,
    active BOOLEAN NOT NULL DEFAULT TRUE
);

-- Store questions configured for individual services.
-- Price and duration effects allow answers to influence quotations.
CREATE TABLE service_questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    question_text VARCHAR(255) NOT NULL,
    input_type ENUM(
        'multiple_choice',
        'price_choice',
        'time_choice',
        'quantity',
        'area'
    ) NOT NULL,
    price_effect DECIMAL(10,2) NOT NULL DEFAULT 0,
    duration_effect INT NOT NULL DEFAULT 0,

    -- Questions are removed if their service is deleted.
    FOREIGN KEY (service_id)
        REFERENCES services(service_id)
        ON DELETE CASCADE
);

-- Store selectable answers for service questions.
-- Each option can have its own price and duration effect.
CREATE TABLE question_options (
    option_id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    option_text VARCHAR(100) NOT NULL,
    price_effect DECIMAL(10,2) NOT NULL DEFAULT 0,
    duration_effect INT NOT NULL DEFAULT 0,

    FOREIGN KEY (question_id)
        REFERENCES service_questions(question_id)
        ON DELETE CASCADE
);

-- Store generated quotations and their calculated results.
-- The status records the quotation's progress through review.
CREATE TABLE quotations (
    quotation_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    property_id INT NOT NULL,
    service_id INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    estimated_duration INT NOT NULL,
    status ENUM(
        'pending',
        'approved',
        'rejected',
        'accepted'
    ) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Link each quotation to its customer, property and service.
    FOREIGN KEY (customer_id)
        REFERENCES customers(customer_id),

    FOREIGN KEY (property_id)
        REFERENCES properties(property_id),

    FOREIGN KEY (service_id)
        REFERENCES services(service_id)
);

-- Store the answers used to generate each quotation.
-- Answers are linked to both the quotation and its question.
CREATE TABLE quotation_answers (
    answer_id INT AUTO_INCREMENT PRIMARY KEY,
    quotation_id INT NOT NULL,
    question_id INT NOT NULL,
    answer_value VARCHAR(255) NOT NULL,

    -- Remove stored answers if their quotation is deleted.
    FOREIGN KEY (quotation_id)
        REFERENCES quotations(quotation_id)
        ON DELETE CASCADE,

    FOREIGN KEY (question_id)
        REFERENCES service_questions(question_id)
);

-- Store the business's working schedule for each weekday.
-- The day number identifies the weekday and is unique.
CREATE TABLE working_hours (
    working_hours_id INT AUTO_INCREMENT PRIMARY KEY,
    day_of_week TINYINT NOT NULL UNIQUE,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_working BOOLEAN NOT NULL DEFAULT TRUE
);

-- Store settings used when calculating booking availability.
-- Daily capacity is measured in minutes.
CREATE TABLE booking_settings (
    setting_id INT AUTO_INCREMENT PRIMARY KEY,
    daily_capacity INT NOT NULL,
    buffer_minutes INT NOT NULL DEFAULT 15
);

-- Store jobs created from confirmed bookings.
-- Actual duration can later be compared with the estimate.
CREATE TABLE jobs (
    job_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL UNIQUE,
    actual_duration INT,
    status ENUM(
        'scheduled',
        'in_progress',
        'completed'
    ) NOT NULL DEFAULT 'scheduled',
    completed_at TIMESTAMP NULL,

    -- Each booking can produce at most one job record.
    FOREIGN KEY (booking_id)
        REFERENCES bookings(booking_id)
);
