-- Drop existing table if exists
DROP TABLE IF EXISTS beritas;

-- Create the correct berita table
CREATE TABLE IF NOT EXISTS berita (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    konten TEXT NOT NULL,
    gambar VARCHAR(255) NULL,
    video VARCHAR(255) NULL,
    link VARCHAR(255) NULL,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    kategori ENUM('umum', 'pengumuman', 'kegiatan', 'pembangunan', 'kesehatan', 'pendidikan', 'ekonomi', 'sosial', 'lingkungan', 'keamanan') DEFAULT 'umum',
    tingkat_akses ENUM('rt', 'rw', 'desa') NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    rt_id BIGINT UNSIGNED NULL,
    rw_id BIGINT UNSIGNED NULL,
    tanggal_publish TIMESTAMP NULL,
    views INT DEFAULT 0,
    is_pinned BOOLEAN DEFAULT FALSE,
    excerpt TEXT NULL,
    tags JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    -- Foreign key constraints
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (rt_id) REFERENCES rts(id) ON DELETE CASCADE,
    FOREIGN KEY (rw_id) REFERENCES rws(id) ON DELETE CASCADE,
    
    -- Indexes
    INDEX idx_status_publish (status, tanggal_publish),
    INDEX idx_tingkat_akses (tingkat_akses, rt_id, rw_id),
    INDEX idx_kategori_status (kategori, status),
    INDEX idx_pinned_publish (is_pinned, tanggal_publish)
);
