<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260510161022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_deliveries (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, delivery_type INT NOT NULL, address LONGTEXT DEFAULT NULL, comment LONGTEXT DEFAULT NULL, INDEX idx_order_delivery_order_id (order_id), INDEX idx_order_delivery_type (delivery_type), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE order_files (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, disk_path VARCHAR(255) NOT NULL, file_name VARCHAR(255) NOT NULL, original_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX idx_order_file_order_id (order_id), INDEX idx_order_file_created_at (created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE order_item_millings (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, source_item_id INT DEFAULT NULL, print_id INT NOT NULL, material VARCHAR(255) NOT NULL, performer_id INT DEFAULT NULL, note LONGTEXT DEFAULT NULL, printed TINYINT NOT NULL, ready TINYINT NOT NULL, price NUMERIC(10, 2) NOT NULL, INDEX idx_order_item_milling_order_id (order_id), INDEX idx_order_item_milling_source_item_id (source_item_id), INDEX idx_order_item_milling_print_id (print_id), INDEX idx_order_item_milling_performer_id (performer_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE order_item_processings (id INT AUTO_INCREMENT NOT NULL, order_item_id INT NOT NULL, processing_id INT NOT NULL, INDEX idx_order_item_processing_item_id (order_item_id), INDEX idx_order_item_processing_processing_id (processing_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE order_items (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, source_item_id INT DEFAULT NULL, print_id INT NOT NULL, product_id INT NOT NULL, material_id INT NOT NULL, option_id INT NOT NULL, dpi_type INT NOT NULL, variant_type INT NOT NULL, width NUMERIC(10, 2) NOT NULL, height NUMERIC(10, 2) NOT NULL, quantity INT NOT NULL, performer_id INT DEFAULT NULL, note LONGTEXT DEFAULT NULL, printed TINYINT NOT NULL, ready TINYINT NOT NULL, price NUMERIC(10, 2) NOT NULL, INDEX idx_order_item_order_id (order_id), INDEX idx_order_item_print_id (print_id), INDEX idx_order_item_product_id (product_id), INDEX idx_order_item_material_id (material_id), INDEX idx_order_item_option_id (option_id), INDEX idx_order_item_dpi_type (dpi_type), INDEX idx_order_item_variant_type (variant_type), INDEX idx_order_item_performer_id (performer_id), INDEX idx_order_item_source_item_id (source_item_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE order_notifications (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, notification_type INT NOT NULL, created_at DATETIME NOT NULL, INDEX idx_order_notification_order_id (order_id), INDEX idx_order_notification_type (notification_type), INDEX idx_order_notification_created_at (created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE order_payments (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, client_id INT NOT NULL, operation_type INT NOT NULL, payment_type INT NOT NULL, reason LONGTEXT DEFAULT NULL, note LONGTEXT DEFAULT NULL, confirmation TINYINT NOT NULL, created_at DATETIME NOT NULL, INDEX idx_order_payment_order_id (order_id), INDEX idx_order_payment_client_id (client_id), INDEX idx_order_payment_operation_type (operation_type), INDEX idx_order_payment_payment_type (payment_type), INDEX idx_order_payment_created_at (created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE order_sections (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, section_type INT NOT NULL, INDEX idx_order_section_order_id (order_id), INDEX idx_order_section_type (section_type), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE order_services (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, service_type INT NOT NULL, price NUMERIC(10, 2) NOT NULL, note LONGTEXT DEFAULT NULL, INDEX idx_order_service_order_id (order_id), INDEX idx_order_service_type (service_type), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, manager_id INT DEFAULT NULL, designer_id INT DEFAULT NULL, client_id INT NOT NULL, status_type INT NOT NULL, storage_type INT NOT NULL, general_note LONGTEXT DEFAULT NULL, extension VARCHAR(32) DEFAULT NULL, accepted_at DATETIME NOT NULL, deadline_at DATETIME NOT NULL, created_at DATETIME NOT NULL, archived_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX idx_order_creator_id (creator_id), INDEX idx_order_manager_id (manager_id), INDEX idx_order_designer_id (designer_id), INDEX idx_order_client_id (client_id), INDEX idx_order_status_type (status_type), INDEX idx_order_storage_type (storage_type), INDEX idx_order_created_at (created_at), INDEX idx_order_accepted_at (accepted_at), INDEX idx_order_deadline_at (deadline_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE order_deliveries');
        $this->addSql('DROP TABLE order_files');
        $this->addSql('DROP TABLE order_item_millings');
        $this->addSql('DROP TABLE order_item_processings');
        $this->addSql('DROP TABLE order_items');
        $this->addSql('DROP TABLE order_notifications');
        $this->addSql('DROP TABLE order_payments');
        $this->addSql('DROP TABLE order_sections');
        $this->addSql('DROP TABLE order_services');
        $this->addSql('DROP TABLE orders');
    }
}
