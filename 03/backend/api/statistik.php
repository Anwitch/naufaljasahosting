<?php
/**
 * statistik.php
 * Tanggung Jawab: Menyediakan data analisis spasial menggunakan Point in Polygon (ST_Contains).
 * Menghitung kepadatan warga miskin dalam setiap kavling.
 */

require_once __DIR__ . '/../../../webgis_app/core_config/database.php';
require_once '../utils/response_helper.php';

$pdo = Database::getConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        // Query Spatial Point-in-Polygon
        // Mengambil Kavling dan menghitung jumlah warga miskin (Point) yang ada di dalamnya
        $sql = "
            SELECT 
                k.id, 
                k.nama_pemilik, 
                k.luas, 
                ST_AsGeoJSON(k.geom) as geojson,
                COUNT(w.id) as jumlah_warga,
                COALESCE(SUM(w.jumlah_tanggungan), 0) as total_tanggungan
            FROM kavling k
            LEFT JOIN warga_miskin w ON ST_Contains(k.geom, w.geom)
            GROUP BY k.id
        ";
        
        $stmt = $pdo->query($sql);
        
        $features = [];
        while ($row = $stmt->fetch()) {
            $jumlah = (int) $row['jumlah_warga'];
            
            $features[] = [
                'type' => 'Feature',
                'geometry' => json_decode($row['geojson']),
                'properties' => [
                    'id' => $row['id'],
                    'nama' => $row['nama_pemilik'],
                    'nama_pemilik' => $row['nama_pemilik'],
                    'luas' => $row['luas'],
                    'jumlah_warga_miskin' => $jumlah,
                    'jumlah_warga' => $jumlah,
                    'total_tanggungan' => (int) $row['total_tanggungan']
                ]
            ];
        }
        
        sendSuccess(['type' => 'FeatureCollection', 'features' => $features], 'Data statistik spasial berhasil dihitung');
    } catch (PDOException $e) {
        sendError('Gagal menghitung statistik spasial: ' . $e->getMessage(), 500);
    }
} else {
    sendError('Method not allowed', 405);
}
?>
