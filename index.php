<?php
// index.php (Bài tập cá nhân - Giao diện nâng cấp)
session_start();
require_once 'db.php';
require_once 'HocKyModel.php';

$model = new HocKyModel($pdo);
$action = $_GET['action'] ?? 'list';

// Hiển thị thông báo Flash
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

// Xử lý XÓA
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $model->delete($id);
    $_SESSION['message'] = "Xóa học kỳ thành công!";
    header('Location: index.php');
    exit;
}

// Xử lý THÊM MỚI
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = trim($_POST['ten_hoc_ky']);
    $bat_dau = $_POST['ngay_bat_dau'];
    $ket_thuc = $_POST['ngay_ket_thuc'];
    
    if ($ten === '' || $bat_dau === '' || $ket_thuc === '') {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    } elseif ($bat_dau > $ket_thuc) {
        $error = "Ngày kết thúc phải sau ngày bắt đầu.";
    } else {
        $model->insert($ten, $bat_dau, $ket_thuc);
        $_SESSION['message'] = "Thêm học kỳ thành công!";
        header('Location: index.php');
        exit;
    }
}

// Xử lý CẬP NHẬT
if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $ten = trim($_POST['ten_hoc_ky']);
    $bat_dau = $_POST['ngay_bat_dau'];
    $ket_thuc = $_POST['ngay_ket_thuc'];

    if ($ten === '' || $bat_dau === '' || $ket_thuc === '') {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    } elseif ($bat_dau > $ket_thuc) {
        $error = "Ngày kết thúc phải sau ngày bắt đầu.";
    } else {
        $model->update($id, $ten, $bat_dau, $ket_thuc);
        $_SESSION['message'] = "Cập nhật học kỳ thành công!";
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài tập cá nhân - Quản lý Học Kỳ</title>
    <!-- Nhúng Google Fonts cho đẹp -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --bg-page: #f4f7fb;
            --surface: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --danger: #dc2626;
            --danger-bg: #fef2f2;
            --success: #059669;
            --success-bg: #ecfdf5;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-page); color: var(--text-main); padding: 30px; }
        
        .container { max-width: 1200px; margin: 0 auto; }
        
        .page-header { margin-bottom: 25px; }
        .page-header h2 { font-size: 24px; font-weight: 700; color: var(--text-main); }
        .page-header p { color: var(--text-muted); font-size: 14px; margin-top: 5px; }

        /* Layout 2 cột giống bài của Ngọc Anh */
        .layout-grid { display: grid; grid-template-columns: 350px 1fr; gap: 25px; align-items: start; }

        /* Style cho các khối Card */
        .card { background: var(--surface); border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; }
        .card-header { padding: 18px 20px; border-bottom: 1px solid var(--border); background: #f8fafc; }
        .card-header h3 { font-size: 16px; font-weight: 600; color: var(--text-main); }
        .card-body { padding: 20px; }

        /* Form */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; }
        .form-group input { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; transition: 0.2s; }
        .form-group input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        
        /* Nút bấm */
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 10px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; transition: 0.2s; text-decoration: none; }
        .btn-primary { background: var(--primary); color: #fff; width: 100%; }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-secondary { background: #f1f5f9; color: #475569; width: 100%; margin-top: 10px; }
        .btn-secondary:hover { background: #e2e8f0; }
        
        /* Nút trong bảng */
        .btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 6px; cursor: pointer; text-decoration: none; font-weight: 500; border: none; display: inline-block;}
        .btn-edit { background: #fef3c7; color: #b45309; margin-right: 5px; }
        .btn-edit:hover { background: #fde68a; }
        .btn-delete { background: #fee2e2; color: #b91c1c; }
        .btn-delete:hover { background: #fecaca; }

        /* Thanh tìm kiếm */
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-bar input { flex: 1; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; }
        .search-bar input:focus { border-color: var(--primary); }
        .search-bar button { width: auto; padding: 10px 20px; }

        /* Bảng dữ liệu */
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 12px 15px; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; background: #f8fafc; border-bottom: 1px solid var(--border); white-space: nowrap; }
        td { padding: 15px; font-size: 14px; border-bottom: 1px solid var(--border); color: #334155; vertical-align: middle; }
        tbody tr:hover { background: #f8fafc; }

        /* Thông báo */
        .alert { padding: 12px 15px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; font-weight: 500; }
        .alert-success { background: var(--success-bg); color: var(--success); border: 1px solid #a7f3d0; }
        .alert-danger { background: var(--danger-bg); color: var(--danger); border: 1px solid #fecaca; }

        /* Phân trang */
        .pagination { display: flex; gap: 5px; margin-top: 20px; justify-content: flex-end; }
        .page-link { padding: 8px 14px; border: 1px solid var(--border); border-radius: 6px; text-decoration: none; color: var(--text-main); font-weight: 500; font-size: 14px; background: #fff; }
        .page-link:hover { background: #f1f5f9; }
        .page-link.active { background: var(--primary); color: #fff; border-color: var(--primary); }

        @media (max-width: 900px) {
            .layout-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="page-header">
        <h2>Hệ thống Quản lý Học kỳ</h2>
        <p>Bài tập cá nhân - Môn Lập trình Web</p>
    </div>

    <?php if ($message): ?><div class="alert alert-success"><?= $message ?></div><?php endif; ?>
    <?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

    <div class="layout-grid">
        
        <!-- CỘT TRÁI: FORM THÊM/SỬA -->
        <div class="card">
            <div class="card-header">
                <h3><?= ($action === 'edit') ? '✏️ Cập nhật Học kỳ' : '➕ Thêm Học kỳ mới' ?></h3>
            </div>
            <div class="card-body">
                <?php 
                    $is_edit = ($action === 'edit');
                    $row = $is_edit ? $model->getById((int)$_GET['id']) : null;
                ?>
                <form method="POST" action="index.php?action=<?= $is_edit ? 'edit' : 'create' ?>">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Tên học kỳ *</label>
                        <input type="text" name="ten_hoc_ky" required value="<?= $is_edit ? htmlspecialchars($row['ten_hoc_ky']) : '' ?>" placeholder="VD: Học kỳ 1 năm 2026-2027">
                    </div>
                    
                    <div class="form-group">
                        <label>Ngày bắt đầu *</label>
                        <input type="date" name="ngay_bat_dau" required value="<?= $is_edit ? $row['ngay_bat_dau'] : '' ?>">
                    </div>

                    <div class="form-group">
                        <label>Ngày kết thúc *</label>
                        <input type="date" name="ngay_ket_thuc" required value="<?= $is_edit ? $row['ngay_ket_thuc'] : '' ?>">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <?= $is_edit ? 'Lưu thay đổi' : 'Thêm mới' ?>
                    </button>
                    
                    <?php if ($is_edit): ?>
                        <a href="index.php" class="btn btn-secondary">Hủy bỏ</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- CỘT PHẢI: DANH SÁCH & TÌM KIẾM -->
        <div class="card">
            <div class="card-body">
                <!-- Thanh tìm kiếm -->
                <form method="GET" action="index.php" class="search-bar">
                    <input type="text" name="keyword" placeholder="Tìm kiếm theo tên học kỳ..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                    <?php if(isset($_GET['keyword'])): ?>
                        <a href="index.php" class="btn btn-secondary" style="width: auto; margin-top: 0; padding: 10px 15px;">Hủy</a>
                    <?php endif; ?>
                </form>

                <!-- Bảng dữ liệu -->
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên Học Kỳ</th>
                                <th>Bắt đầu</th>
                                <th>Kết thúc</th>
                                <th style="text-align: right;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $keyword = trim($_GET['keyword'] ?? '');
                                $page = max(1, (int)($_GET['page'] ?? 1));
                                $perPage = 5;
                                $total = $model->countAll($keyword);
                                $totalPages = ceil($total / $perPage);
                                $list = $model->getAll($keyword, $page, $perPage);
                            ?>
                            <?php if (empty($list)): ?>
                                <tr><td colspan="5" style="text-align: center; color: #64748b;">Không tìm thấy dữ liệu.</td></tr>
                            <?php else: ?>
                                <?php foreach ($list as $item): ?>
                                <tr>
                                    <td><?= $item['id'] ?></td>
                                    <td style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($item['ten_hoc_ky']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($item['ngay_bat_dau'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($item['ngay_ket_thuc'])) ?></td>
                                    <td style="text-align: right;">
                                        <a href="index.php?action=edit&id=<?= $item['id'] ?>" class="btn-sm btn-edit">Sửa</a>
                                        <form method="POST" action="index.php?action=delete" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <button type="submit" class="btn-sm btn-delete">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="index.php?page=<?= $i ?>&keyword=<?= urlencode($keyword) ?>" 
                           class="page-link <?= $i === $page ? 'active' : '' ?>">
                           <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>

            </div>
        </div>

    </div>
</div>

</body>
</html>