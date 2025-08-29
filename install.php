<?php
ob_start();
?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài Đặt Media Manager</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fb;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .step {
            text-align: center;
            flex: 1;
            position: relative;
        }
        
        .step:not(:last-child):after {
            content: '';
            position: absolute;
            top: 15px;
            right: -50%;
            width: 100%;
            height: 2px;
            background: #ddd;
            z-index: 1;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            background: #ddd;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            position: relative;
            z-index: 2;
        }
        
        .step.active .step-number {
            background: #3498db;
        }
        
        .step.completed .step-number {
            background: #2ecc71;
        }
        
        .step-label {
            font-size: 14px;
            color: #777;
        }
        
        .step.active .step-label {
            color: #3498db;
            font-weight: bold;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #3498db;
            outline: none;
        }
        
        .btn {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #2980b9;
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ef9a9a;
        }
        
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }
        
        .alert-info {
            background: #e3f2fd;
            color: #1565c0;
            border: 1px solid #bbdefb;
        }
        
        .alert-warning {
            background: #fff8e1;
            color: #ff8f00;
            border: 1px solid #ffecb3;
        }
        
        .requirement-list {
            list-style-type: none;
        }
        
        .requirement-list li {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }
        
        .requirement-list li:before {
            content: '✓';
            color: #2ecc71;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .requirement-list li.error:before {
            content: '✗';
            color: #e74c3c;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mt-20 {
            margin-top: 20px;
        }
        
        .help-box {
            background: #f9f9f9;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 15px 0;
            border-radius: 0 4px 4px 0;
        }
        
        .help-box h3 {
            margin-top: 0;
            color: #3498db;
        }
        
        .help-box code {
            background: #eee;
            padding: 2px 5px;
            border-radius: 3px;
        }
        
        .completed-container {
            text-align: center;
            padding: 40px 20px;
        }
        
        .completed-icon {
            font-size: 64px;
            color: #2ecc71;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .step:not(:last-child):after {
                display: none;
            }
            
            .step-indicator {
                flex-direction: column;
                gap: 15px;
            }
            
            .step {
                display: flex;
                align-items: center;
                text-align: left;
            }
            
            .step-number {
                margin: 0 15px 0 0;
            }
        }
    </style>
</head>
<body>
    <?php
    // Thiết lập biến và hàm kiểm tra
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Không sử dụng session
    // session_start();

    // Kiểm tra yêu cầu hệ thống
    function checkRequirements() {
        $errors = [];
        
        // Kiểm tra phiên bản PHP
        if (version_compare(PHP_VERSION, '8.2.0') < 0) {
            $errors[] = "PHP phiên bản 8.2 hoặc cao hơn là bắt buộc. Phiên bản hiện tại: " . PHP_VERSION;
        }
        
        // Kiểm tra file cài đặt
        if (!file_exists('upload_install.sql')) {
            $errors[] = "File upload_install.sql không tồn tại.";
        }
        
        // Kiểm tra extension cần thiết
        $required_extensions = ['pdo', 'pdo_mysql', 'gd'];
        foreach ($required_extensions as $ext) {
            if (!extension_loaded($ext)) {
                $errors[] = "Extension $ext là bắt buộc nhưng chưa được cài đặt.";
            }
        }
        
        // Kiểm tra quyền ghi
        if (!is_writable('.')) {
            $errors[] = "Thư mục hiện tại không có quyền ghi. Vui lòng cấp quyền ghi.";
        }
        
        return $errors;
    }

    // Kiểm tra xem đã cài đặt chưa bằng cách kiểm tra file _db_config.php
    $config_file_exists = file_exists('_db_config.php');
    
    // Xác định step
    $step = 1;
    if (isset($_GET['step'])) {
        $step = (int)$_GET['step'];
    }
    
    // Nếu đã cài đặt, chuyển đến step 3
    if ($config_file_exists && $step < 3) {
        $step = 3;
    }
    
    // Kiểm tra yêu cầu hệ thống
    $requirementErrors = checkRequirements();
    
    // Xử lý form cài đặt
    $error = '';
    $success = '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install'])) {
        $db_host = $_POST['db_host'] ?? 'localhost';
        $db_user = $_POST['db_user'] ?? '';
        $db_pass = $_POST['db_pass'] ?? '';
        $db_name = $_POST['db_name'] ?? '';
        
        try {
            // Kết nối MySQL
            $dsn = "mysql:host=$db_host;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, $db_user, $db_pass, $options);
            
            // Kiểm tra database có tồn tại không
            $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?");
            $stmt->execute([$db_name]);
            if ($stmt->rowCount() === 0) {
                throw new Exception("Database '$db_name' không tồn tại. Vui lòng tạo thủ công trước khi cài đặt.");
            }

            // Chọn database
            $pdo->exec("USE `$db_name`");

            // Xóa tất cả bảng trước khi import
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            foreach ($tables as $table) {
                $pdo->exec("DROP TABLE IF EXISTS `$table`");
            }
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

            // Import dữ liệu từ file SQL
            $sql = file_get_contents('upload_install.sql');
            if ($sql === false) {
                throw new Exception("Không thể đọc file SQL.");
            }
            $pdo->exec($sql);

            // Ghi file cấu hình
            $config_content = "<?php\n";
            $config_content .= "define('db_host', '$db_host');\n";
            $config_content .= "define('db_user', '$db_user');\n";
            $config_content .= "define('db_pass', '$db_pass');\n";
            $config_content .= "define('db_name', '$db_name');\n";
            $config_content .= "?>";
            
            if (file_put_contents('_db_config.php', $config_content) === false) {
                throw new Exception("Không thể tạo file cấu hình database.");
            }

            $success = "Cài đặt thành công!";
            $step = 3;
        } catch (Exception $e) {
            $error = "Lỗi: " . $e->getMessage();
        }
    }
    ?>
    
    <div class="container">
        <h1>Cài Đặt Media Manager</h1>
        
        <div class="step-indicator">
            <div class="step <?php echo $step >= 1 ? 'active' : ''; ?> <?php echo $step > 1 ? 'completed' : ''; ?>">
                <div class="step-number">1</div>
                <div class="step-label">Kiểm tra hệ thống</div>
            </div>
            <div class="step <?php echo $step >= 2 ? 'active' : ''; ?> <?php echo $step > 2 ? 'completed' : ''; ?>">
                <div class="step-number">2</div>
                <div class="step-label">Cấu hình Database</div>
            </div>
            <div class="step <?php echo $step >= 3 ? 'active' : ''; ?>">
                <div class="step-number">3</div>
                <div class="step-label">Hoàn tất</div>
            </div>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if(!empty($requirementErrors) && $step !=1){
            header("Location: /install.php?step=1");
            exit();
        } ?>
        <?php if ($step == 1): ?>
            <div class="card">
                <h2>Kiểm tra yêu cầu hệ thống</h2>
                <p>Trước khi cài đặt, hệ thống sẽ kiểm tra các yêu cầu cần thiết.</p>
                
                <ul class="requirement-list">
                    <li class="<?php echo version_compare(PHP_VERSION, '8.2.0') >= 0 ? '' : 'error'; ?>">
                        PHP phiên bản 8.2+ (Hiện tại: <?php echo PHP_VERSION; ?>)
                    </li>
                    <li class="<?php echo file_exists('upload_install.sql') ? '' : 'error'; ?>">
                        File upload_install.sql <?php echo file_exists('upload_install.sql') ? 'tồn tại' : 'không tồn tại'; ?>
                    </li>
                    <li class="<?php echo extension_loaded('pdo') ? '' : 'error'; ?>">
                        PDO Extension <?php echo extension_loaded('pdo') ? 'đã cài đặt' : 'chưa cài đặt'; ?>
                    </li>
                    <li class="<?php echo extension_loaded('pdo_mysql') ? '' : 'error'; ?>">
                        PDO MySQL Extension <?php echo extension_loaded('pdo_mysql') ? 'đã cài đặt' : 'chưa cài đặt'; ?>
                    </li>
                    <li class="<?php echo extension_loaded('gd') ? '' : 'error'; ?>">
                        GD Extension <?php echo extension_loaded('gd') ? 'đã cài đặt' : 'chưa cài đặt'; ?>
                    </li>
                    <li class="<?php echo is_writable('.') ? '' : 'error'; ?>">
                        Thư mục hiện tại có quyền ghi <?php echo is_writable('.') ? 'có' : 'không'; ?>
                    </li>
                </ul>
                
                <?php if (empty($requirementErrors)): ?>
                    <div class="text-center mt-20">
                        <a href="?step=2" class="btn">Tiếp tục</a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-error">
                        Vui lòng khắc phục các lỗi trên trước khi tiếp tục.
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif ($step == 2 && !$config_file_exists): ?>

            <div class="card">
                <h2>Cấu hình Database</h2>
                <p>Vui lòng nhập thông tin kết nối database của bạn.</p>
                
                <form method="POST" action="?step=2">
                    <div class="form-group">
                        <label for="db_host">Database Host</label>
                        <input type="text" id="db_host" name="db_host" value="localhost" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="db_user">Database User</label>
                        <input type="text" id="db_user" name="db_user" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="db_pass">Database Password</label>
                        <input type="password" id="db_pass" name="db_pass">
                    </div>
                    
                    <div class="form-group">
                        <label for="db_name">Database Name</label>
                        <input type="text" id="db_name" name="db_name" required>
                    </div>
                    
                    <div class="alert alert-warning">
                        <strong>Cảnh báo:</strong> Tất cả dữ liệu trong database sẽ bị xóa và thay thế bằng dữ liệu mới.
                    </div>
                    
                    <button type="submit" name="install" class="btn btn-block">Cài đặt ngay</button>
                </form>
            </div>
        <?php elseif ($step == 3 || $config_file_exists): ?>
            <div class="card">
                <div class="completed-container">
                    <div class="completed-icon">✓</div>
                    <h2>Cài đặt thành công!</h2>
                    <p>Tài Khoản quản trị: <b class="alert-error">admin</b>/<b class="alert-error">12345</b>, <i>Vui lòng thay đổi mật khẩu sau khi đăng nhập thành công</i></p>
                    <p>Media Manager đã được cài đặt thành công vào hệ thống của bạn.</p>
                    <p><a href="/index.php">Bấm vào đây</a> đề về trang chủ.</p>
                    
                    <div class="alert alert-info mt-20">
                        <strong>Lưu ý quan trọng:</strong> Vì lý do bảo mật, vui lòng xóa file install.php sau khi cài đặt.
                    </div>
                    
                    <?php if ($config_file_exists): ?>
                        <div class="alert alert-warning mt-20">
                            <strong>Ghi chú:</strong> Hệ thống đã phát hiện file cấu hình database tồn tại. 
                            Nếu bạn muốn cài đặt lại, vui lòng xóa file <code>_db_config.php</code> trước.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            
        <?php endif; ?>
    </div>
</body>
</html>
