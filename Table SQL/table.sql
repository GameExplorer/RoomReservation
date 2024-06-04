CREATE TABLE reserva_de_habitacion (
    id_ticket INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(25) NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_finalizacion TIME NOT NULL,
    reserva_creada DATE NOT NULL,
    reserva_reservada DATE NOT NULL
);


