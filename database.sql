CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150),
  svg VARCHAR(255)
);

CREATE TABLE subcategorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  categoria_id INT NOT NULL,
  nome VARCHAR(150),
  imagem VARCHAR(255),
  FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

CREATE TABLE lojas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150),
  whatsapp VARCHAR(20),
  logo VARCHAR(255),
  endereco VARCHAR(255),
  descricao TEXT,
  telefone VARCHAR(20),
  horario VARCHAR(120)
);

CREATE TABLE produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  loja_id INT,
  categoria_id INT,
  subcategoria_id INT,
  nome VARCHAR(150),
  preco DECIMAL(10,2),
  imagem VARCHAR(255),
  em_promocao TINYINT(1) NOT NULL DEFAULT 0,
  porcentagem_promocao DECIMAL(5,2) NOT NULL DEFAULT 0,
  tipo_desconto VARCHAR(20) NOT NULL DEFAULT 'nenhum',
  valor_desconto DECIMAL(10,2) NOT NULL DEFAULT 0,
  data_fim_promocao DATE NULL,
  preco_alternativo DECIMAL(10,2) NULL,
  texto_alternativo VARCHAR(255) NULL,
  descricao TEXT,
  FOREIGN KEY (loja_id) REFERENCES lojas(id),
  FOREIGN KEY (categoria_id) REFERENCES categorias(id),
  FOREIGN KEY (subcategoria_id) REFERENCES subcategorias(id)
);

CREATE TABLE admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE metricas (
  id INT PRIMARY KEY,
  vendas_whatsapp INT NOT NULL DEFAULT 0
);

INSERT INTO metricas (id, vendas_whatsapp) VALUES
  (1, 0);

INSERT INTO admin_users (username, password_hash) VALUES
  ('ComercioUniao', '$2y$10$qHOq/PRVrKn5NTiiVwb8pubHRvxmdJnn2un2B82rDoOX/oXWW3Vxi');
