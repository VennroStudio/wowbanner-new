<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260428100014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE material_options (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, material_id INT NOT NULL, pricing_type INT NOT NULL, is_cut TINYINT NOT NULL, INDEX idx_material_option_material_id (material_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE material_pricing_by_area (id INT AUTO_INCREMENT NOT NULL, material_id INT NOT NULL, option_id INT NOT NULL, dpi_type INT NOT NULL, area_range_type INT NOT NULL, price NUMERIC(10, 2) NOT NULL, cost NUMERIC(10, 2) NOT NULL, print_hours NUMERIC(10, 2) NOT NULL, INDEX idx_m_pricing_area_material_id (material_id), INDEX idx_m_pricing_area_option_id (option_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE material_pricing_by_piece (id INT AUTO_INCREMENT NOT NULL, material_id INT NOT NULL, option_id INT NOT NULL, variant_type INT NOT NULL, price NUMERIC(10, 2) NOT NULL, cost NUMERIC(10, 2) NOT NULL, print_hours NUMERIC(10, 2) NOT NULL, INDEX idx_m_pricing_piece_material_id (material_id), INDEX idx_m_pricing_piece_option_id (option_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE material_pricing_cuts (id INT AUTO_INCREMENT NOT NULL, material_id INT NOT NULL, option_id INT NOT NULL, type INT NOT NULL, price NUMERIC(10, 2) NOT NULL, INDEX idx_m_pricing_cut_material_id (material_id), INDEX idx_m_pricing_cut_option_id (option_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE material_processings (id INT AUTO_INCREMENT NOT NULL, material_id INT NOT NULL, option_id INT NOT NULL, processing_id INT NOT NULL, INDEX idx_material_processing_material_id (material_id), INDEX idx_material_processing_option_id (option_id), INDEX idx_material_processing_processing_id (processing_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product_materials (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, material_option_id INT NOT NULL, INDEX idx_product_id (product_id), INDEX idx_material_option_id (material_option_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product_prints (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, print_id INT NOT NULL, INDEX idx_product_id (product_id), INDEX idx_print_id (print_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, INDEX idx_name (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE material_options');
        $this->addSql('DROP TABLE material_pricing_by_area');
        $this->addSql('DROP TABLE material_pricing_by_piece');
        $this->addSql('DROP TABLE material_pricing_cuts');
        $this->addSql('DROP TABLE material_processings');
        $this->addSql('DROP TABLE product_materials');
        $this->addSql('DROP TABLE product_prints');
        $this->addSql('DROP TABLE products');
    }
}
