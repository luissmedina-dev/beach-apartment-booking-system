/* 
    ===============  USERS ==================
    Responsável por armazenar os usuários do sistema.
        id = Identificador único do usuário.
        name = Nome completo.
        email = Email do usuário.
        password = Senha criptografada.
        role = Tipo de usuário. (admin, client)
        created_at = Data de criação da conta.
    ==========================================
*/

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(200),
    role VARCHAR(100),
    created_at DATE,
    updated_at DATE
);

/* 
    ===============  RESERVATIONS ==================
    Responsável por armazenar as reservas.
        id = Identificador único do usuário.
        user_id = Qual usuário fez a reserva.
        checkin_date = Data de entrada.
        checkout_date = Data de saída.
        total_price = Valor total da hospedagem.
        status = Situação da reserva (pending, confirmed, cancelled, completed)
        created_at = Quando a reserva foi criada.
    =================================================
*/

CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, 
    checkin_date DATE NOT NULL,
    checkout_date DATE NOT NULL,
    total_price DECIMAL(10,2),
    status VARCHAR(100) DEFAULT 'solicitado',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

/* 
    ===============  EXPENSES ==================
    Responsável por armazenar despesas do apartamento.
        id = Identificador único do usuário.
        description = Descrição da despesa (Conta de energia, Limpeza, Troca de chuveiro)
        category = Categoria (energy, internet, maintenance, cleaning, condominium)
        value = Valor da despesa.
        expense_date = Data da despesa.
        created_at = Quando a reserva foi criada.
    =============================================
*/

CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(200),
    category VARCHAR (100),
    value DECIMAL(10,2),
    expense_date DATE,
    created_at DATE,
    updated_at DATE
);

/* 
    ===============  PROPERTY IMAGES ==================
    Responsável por armazenar despesas do apartamento.
        id = Identificador único do usuário.
        image_name = Nome amigável (Sala principal, Quarto casal, Sacada).
        image_path = Caminho da imagem (uploads/properties/sala.jpg).
        created_at = Quando a reserva foi criada.
    ====================================================
*/

CREATE TABLE properties_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_name VARCHAR(200),
    image_path VARCHAR(200),
    created_at DATE,
    updated_at DATE
);