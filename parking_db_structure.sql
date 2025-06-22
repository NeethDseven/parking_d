CREATE DATABASE IF NOT EXISTS parking_db;
USE parking_db;

CREATE TABLE abonnements (
  id int(11) NOT NULL AUTO_INCREMENT,
  nom varchar(50) NOT NULL,
  duree varchar(20) NOT NULL,
  reduction decimal(5,2) NOT NULL,
  free_minutes int(11) DEFAULT 0,
  description text DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  price decimal(10,2) DEFAULT NULL COMMENT 'Prix de l\'abonnement',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE alertes_disponibilite (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  place_id int(11) NOT NULL,
  date_debut datetime NOT NULL,
  date_fin datetime NOT NULL,
  statut enum('en_attente','notifiee','expiree') DEFAULT 'en_attente',
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  KEY user_id (user_id),
  KEY place_id (place_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE availability_alerts (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  place_id int(11) NOT NULL,
  date_debut datetime NOT NULL,
  date_fin datetime NOT NULL,
  created_at datetime DEFAULT current_timestamp(),
  notified tinyint(1) DEFAULT 0,
  expires_at datetime NOT NULL,
  PRIMARY KEY (id),
  KEY user_id (user_id),
  KEY place_id (place_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE factures (
  id int(11) NOT NULL AUTO_INCREMENT,
  paiement_id int(11) NOT NULL,
  numero_facture varchar(20) NOT NULL,
  chemin_pdf varchar(255) NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  UNIQUE KEY numero_facture (numero_facture),
  KEY paiement_id (paiement_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE horaires_ouverture (
  id int(11) NOT NULL AUTO_INCREMENT,
  jour_semaine int(11) NOT NULL,
  heure_ouverture time NOT NULL,
  heure_fermeture time NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE logs (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) DEFAULT NULL,
  action varchar(50) NOT NULL,
  description text DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  KEY user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE notifications (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  titre varchar(100) NOT NULL,
  message text NOT NULL,
  type enum('reservation','paiement','rappel','system') NOT NULL,
  lu tinyint(1) DEFAULT 0,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  KEY user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE paiements (
  id int(11) NOT NULL AUTO_INCREMENT,
  reservation_id int(11) NOT NULL,
  montant decimal(10,2) NOT NULL,
  mode_paiement enum('carte','paypal','virement') DEFAULT NULL,
  status enum('en_attente','valide','refuse','annule') DEFAULT 'en_attente',
  date_paiement timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  KEY reservation_id (reservation_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE parking_spaces (
  id int(11) NOT NULL AUTO_INCREMENT,
  numero varchar(10) NOT NULL,
  type enum('standard','handicape','electrique','moto/scooter','velo') NOT NULL DEFAULT 'standard',
  status enum('libre','occupe','maintenance') NOT NULL DEFAULT 'libre',
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  UNIQUE KEY numero (numero)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE remboursements (
  id int(11) NOT NULL AUTO_INCREMENT,
  paiement_id int(11) NOT NULL,
  montant decimal(10,2) NOT NULL,
  raison text DEFAULT NULL,
  status enum('en_cours','effectué','refusé') DEFAULT 'en_cours',
  date_demande timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  KEY paiement_id (paiement_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE reservations (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  place_id int(11) NOT NULL,
  date_debut datetime NOT NULL,
  date_fin datetime DEFAULT NULL,
  status enum('en_attente','confirmée','en_cours','terminee','annulée','expirée','en_cours_immediat','en_attente_paiement') NOT NULL DEFAULT 'en_attente',
  code_acces varchar(10) DEFAULT NULL,
  code_sortie varchar(10) DEFAULT NULL,
  montant_total decimal(10,2) DEFAULT 0.00,
  reduction_abonnement decimal(5,2) DEFAULT 0.00,
  notification_sent tinyint(1) DEFAULT 0,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  expiration_time datetime DEFAULT NULL,
  guest_name varchar(255) DEFAULT NULL,
  guest_email varchar(255) DEFAULT NULL,
  guest_phone varchar(20) DEFAULT NULL,
  guest_token varchar(255) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY guest_token (guest_token),
  KEY user_id (user_id),
  KEY place_id (place_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE tarifs (
  id int(11) NOT NULL AUTO_INCREMENT,
  type_place varchar(50) NOT NULL DEFAULT 'standard',
  free_minutes int(11) NOT NULL DEFAULT 0,
  prix_heure decimal(10,2) NOT NULL,
  prix_journee decimal(10,2) NOT NULL,
  prix_mois decimal(10,2) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE tarifs_historique (
  id int(11) NOT NULL AUTO_INCREMENT,
  tarif_id int(11) NOT NULL,
  admin_id int(11) NOT NULL,
  type_place varchar(50) NOT NULL,
  champ_modifie varchar(50) NOT NULL,
  ancien_prix decimal(10,2) DEFAULT NULL,
  nouveau_prix decimal(10,2) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE tarif_historique (
  id int(11) NOT NULL AUTO_INCREMENT,
  tarif_id int(11) NOT NULL,
  admin_id int(11) NOT NULL,
  type_place varchar(50) NOT NULL,
  champ_modifie enum('type_place','prix_heure','prix_journee','prix_mois','free_minutes') NOT NULL,
  ancien_prix decimal(10,2) DEFAULT 0.00,
  nouveau_prix decimal(10,2) DEFAULT 0.00,
  note text DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  KEY tarif_id (tarif_id),
  KEY admin_id (admin_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE users (
  id int(11) NOT NULL AUTO_INCREMENT,
  email varchar(255) NOT NULL,
  telephone varchar(20) DEFAULT NULL,
  password varchar(255) NOT NULL,
  nom varchar(100) DEFAULT NULL,
  prenom varchar(100) DEFAULT NULL,
  role enum('user','admin') DEFAULT 'user',
  is_subscribed tinyint(1) NOT NULL DEFAULT 0,
  notifications_active tinyint(1) DEFAULT 1,
  status enum('actif','inactif') NOT NULL DEFAULT 'actif',
  payment_preferences text DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE user_abonnements (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  abonnement_id int(11) NOT NULL,
  date_debut datetime NOT NULL,
  date_fin datetime NOT NULL,
  status varchar(20) DEFAULT 'actif',
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  payment_id int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY user_id (user_id),
  KEY abonnement_id (abonnement_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Contraintes étrangères
ALTER TABLE alertes_disponibilite
  ADD CONSTRAINT alertes_disponibilite_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id),
  ADD CONSTRAINT alertes_disponibilite_ibfk_2 FOREIGN KEY (place_id) REFERENCES parking_spaces (id);

ALTER TABLE availability_alerts
  ADD CONSTRAINT fk_alert_place FOREIGN KEY (place_id) REFERENCES parking_spaces (id) ON DELETE CASCADE,
  ADD CONSTRAINT fk_alert_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;

ALTER TABLE factures
  ADD CONSTRAINT factures_ibfk_1 FOREIGN KEY (paiement_id) REFERENCES paiements (id);

ALTER TABLE logs
  ADD CONSTRAINT logs_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id);

ALTER TABLE notifications
  ADD CONSTRAINT notifications_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id);

ALTER TABLE paiements
  ADD CONSTRAINT paiements_ibfk_1 FOREIGN KEY (reservation_id) REFERENCES reservations (id);

ALTER TABLE remboursements
  ADD CONSTRAINT remboursements_ibfk_1 FOREIGN KEY (paiement_id) REFERENCES paiements (id);

ALTER TABLE reservations
  ADD CONSTRAINT reservations_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id),
  ADD CONSTRAINT reservations_ibfk_2 FOREIGN KEY (place_id) REFERENCES parking_spaces (id);

ALTER TABLE tarif_historique
  ADD CONSTRAINT fk_tarif_historique_admin FOREIGN KEY (admin_id) REFERENCES users (id) ON DELETE CASCADE,
  ADD CONSTRAINT fk_tarif_historique_tarif FOREIGN KEY (tarif_id) REFERENCES tarifs (id) ON DELETE CASCADE;

ALTER TABLE user_abonnements
  ADD CONSTRAINT user_abonnements_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  ADD CONSTRAINT user_abonnements_ibfk_2 FOREIGN KEY (abonnement_id) REFERENCES abonnements (id);
