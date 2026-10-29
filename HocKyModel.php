<?php
// HocKyModel.php
class HocKyModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // 1. Lấy danh sách + Tìm kiếm + Phân trang
    public function getAll($keyword = '', $page = 1, $perPage = 5) {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM hoc_ky WHERE ten_hoc_ky LIKE :kw ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':kw', '%' . $keyword . '%', PDO::PARAM_STR);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // 2. Đếm tổng số bản ghi (Để tính số trang)
    public function countAll($keyword = '') {
        $sql = "SELECT COUNT(*) FROM hoc_ky WHERE ten_hoc_ky LIKE :kw";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':kw' => '%' . $keyword . '%']);
        return $stmt->fetchColumn();
    }

    // 3. Lấy 1 bản ghi
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM hoc_ky WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // 4. Thêm mới
    public function insert($ten, $bat_dau, $ket_thuc) {
        $sql = "INSERT INTO hoc_ky (ten_hoc_ky, ngay_bat_dau, ngay_ket_thuc) VALUES (:ten, :bat_dau, :ket_thuc)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':ten' => $ten, ':bat_dau' => $bat_dau, ':ket_thuc' => $ket_thuc]);
    }

    // 5. Cập nhật
    public function update($id, $ten, $bat_dau, $ket_thuc) {
        $sql = "UPDATE hoc_ky SET ten_hoc_ky = :ten, ngay_bat_dau = :bat_dau, ngay_ket_thuc = :ket_thuc WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':ten' => $ten, ':bat_dau' => $bat_dau, ':ket_thuc' => $ket_thuc, ':id' => $id]);
    }

    // 6. Xóa
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM hoc_ky WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>