<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com
*/
function boxFolders($folders) {
	GLOBAL $domain,$admin,$panelFolders;
	$thumb = getThumbFolders($folders['_thumb'], $folders['id']);
	if($admin) {

	}

	return '
	<div class="box '.$folders['_tinhtrang'].'" _thumb = "'.$folders['_thumb'].'" _type="'.$folders['_type'].'" _name="'.$folders['_name'].'" _time="" _byid="'.$folders['_byid'].'" _uid="'.$folders['_uid'].'" id="'.$folders['id'].'" _token="'.$folders['_token'].'" _box="folder">
	
	<div class="thumb">
	<a href="/folders/'.cvLink($folders['_name']).$domain.'/'.$folders['id'].'#files"><img _src="'.$thumb.'" class="ithumb" src="'.$thumb.'" alt="'.delBBCODE($folders['_name']).' - '.$domain.'"></a>
	</div>
	<a href="/folders/'.cvLink($folders['_name']).$domain.'/'.$folders['id'].'">
	<div class="info-list">
	<div class="list-name">'.bbcode($folders['_name']).'</div>
	<div class="list-time">
	'.date('H:i:s - d/m/Y',$folders["_time"]).'
	</div>
	</a>
	'.panelFolders($folders['id']).'
	</div>
	</div>

	';
}

function createFolders($user, $name, $path) {
    if (!isset($user['id'])) { 
        return "<li>Bạn phải đăng nhập để sử dụng chức năng này.</li>";
    }

    if (empty($name)) {
        return "<li>Tên thư mục không được để trống.</li>";
    }

    if ($path != 0) {
        $sFolders = _fetch("SELECT * FROM folders WHERE id='$path' AND _uid='".$user['id']."'");
        if (!isset($sFolders['id'])) {
            return "<li>Thư mục cha không thuộc quyền của bạn.</li>";
        }
    }

    $tieude = _sql($name);
    $token = md5($tieude . time() . rand(1, 99999));
    $keysearch = keySearch($path) . ' ' . $tieude;
    $time = time();

    $insert = _query("INSERT INTO folders(_uid, _byid, _name, _tinhtrang, _token, _time, keysearch) 
                      VALUES('".$user['id']."', '$path', '$tieude', 'danghoatdong', '$token', '$time', '$keysearch')");

    if ($insert) {
        $layid = _fetch("SELECT id FROM folders WHERE _token='$token'");
        return "<li>Tạo thư mục thành công. ID: ".$layid['id']."</li>";
    } else {
        return "<li>Xảy ra lỗi khi thêm.</li>";
    }
}
function getFolders($id = null) {
    if ($id) {
        $query = _fetch("SELECT `id`, `_uid`, `_byid`, `_name`, `_content`, `_thumb`, `_type`, `_tinhtrang`, `keysearch`, `_token`, `_time` 
                         FROM `folders` WHERE id = '$id' LIMIT 1");
        if ($query) {
            return $query; // Trả về dữ liệu nếu có
        }
    }

    // Mảng mặc định nếu không tìm thấy ID
    return [
        'id'         => 0,
        '_uid'       => 0,
        '_byid'      => 0,
        '_name'      => 'Thư mục mặc định',
        '_content'   => '',
        '_thumb'     => 'default.png',
        '_type'      => 'folder',
        '_tinhtrang' => 'inactive',
        'keysearch'  => '',
        '_token'     => '',
        '_time'      => time()
    ];
}

function fPath($id) {
	GLOBAL $domain;
    $folder = _fetch("SELECT * FROM folders WHERE id='"._sql($id)."'");
    
    if (isset($folder['id'])) {
        if ($folder['_byid'] == 0) {
            $return = '<a href="/">'.$domain.'</a> > <a href="/folders/'.cvLink($folder['_name']).$domain.'/'.$folder['id'].'#files">'.bbcode($folder['_name']).'</a>';
        } else {
            $return = fPath($folder['_byid']) . " > " . '<a href="/folders/'.cvLink($folder['_name']).$domain.'/'.$folder['id'].'#files">'.bbcode($folder['_name']).'</a>';
        }
    } else if($id == 0) {
        return '<a href="/">'.$domain.'</a>';
    } else {
        $return = "_404_";
    }
    
    return $return;
}

function panelFolders($id,$act = "showPanel"){
	GLOBAL $u_panelFolders,$admin;
	if(!$admin or $id == 0) {return '';}
	if($act == "showPanel") {
		return '<div class="panel"><a href="'.$u_panelFolders.'?id='.$id.'&act=panelFolders">Sửa</a></div>';
	}
}
function getFolderOptions($parent_id = 0, $prefix = '') {
    // Truy vấn chỉ lấy các thư mục có _byid = $parent_id
    $folders = _fetch_all("SELECT * FROM folders WHERE _byid = '$parent_id' ORDER BY id ASC");
    $options = '';

    if ($folders) {
        foreach ($folders as $folder) {
            $options .= '<option value="'.$folder['id'].'">'.$folder['id'].'.'.$prefix.$folder['_name'].'</option>';
            
            // Kiểm tra nếu có thư mục con thì mới gọi đệ quy
            $options .= getFolderOptions($folder['id'], $prefix . '--');
        }
    }

    return $options;
}

function getThumbFolders1($id) {
    // Bước 1: Kiểm tra _thumb của folder hiện tại
    $folder = _fetch("SELECT id, _thumb FROM folders WHERE id = '" . _sql($id) . "' LIMIT 1");
    if ($folder && !empty($folder['_thumb'])) {
        return $folder['_thumb']; // Trả về _thumb nếu có
    }

    // Bước 2: Kiểm tra _thumb của folder con mới nhất
    $subfolder = _fetch("SELECT id, _thumb FROM folders WHERE _byid = '" . _sql($id) . "' ORDER BY id DESC LIMIT 1");
    if ($subfolder) {
        if (!empty($subfolder['_thumb'])) {
            // Cập nhật _thumb vào folder cha nếu có
            _query("UPDATE folders SET _thumb = '" . _sql($subfolder['_thumb']) . "' WHERE id = '" . _sql($id) . "'");
            return $subfolder['_thumb'];
        }

        // Đệ quy kiểm tra sâu hơn trong folder con nếu _thumb vẫn là null
        $subThumb = getThumbFolders1($subfolder['id']);
        if (!empty($subThumb)) {
            // Cập nhật _thumb vào folder cha nếu có
            _query("UPDATE folders SET _thumb = '" . _sql($subThumb) . "' WHERE id = '" . _sql($id) . "'");
            return $subThumb;
        }
    }

    // Bước 3: Kiểm tra _thumb của file mới nhất trong folder hiện tại
    $file = _fetch("SELECT _thumb FROM files WHERE _byid = '" . _sql($id) . "' ORDER BY id DESC LIMIT 1");
    if ($file && !empty($file['_thumb'])) {
        // Cập nhật _thumb vào folder cha nếu có
        _query("UPDATE folders SET _thumb = '" . _sql($file['_thumb']) . "' WHERE id = '" . _sql($id) . "'");
        return $file['_thumb'];
    }

    // Bước 4: Nếu không tìm thấy _thumb hợp lệ, không làm gì cả (không cập nhật vào database)
    return null; // Không trả về hình mặc định mà trả về null
}

function getThumbFolders($thumb, $id) {
    // Nếu _thumb trống, gọi hàm đệ quy
    if (empty($thumb)) {
        $result = getThumbFolders1($id);
        // Nếu không có _thumb hợp lệ, không cập nhật hình mặc định vào cơ sở dữ liệu
        if ($result === null) {
            return '/asset/image/folders-thumb-default.png'; // Trả về hình mặc định cho trường hợp này
        }
        return $result; // Trả về _thumb nếu đã có
    }
    return $thumb; // Trả về _thumb nếu đã có
}




function getRootFolder($id) {
    // Lấy thông tin của file theo id
    $file = _fetch("SELECT _byid FROM files WHERE id = '" . _sql($id) . "' LIMIT 1");

    // Nếu không tìm thấy file, trả về null
    if (!$file) {
        return null;
    }

    // Lấy _byid của file (tức là thư mục cha)
    $parent_id = $file['_byid'];

    // Lặp lại kiểm tra các thư mục cha cho đến khi tìm thấy thư mục có _byid = 0
    while ($parent_id != 0) {
        // Lấy thông tin thư mục cha
        $folder = _fetch("SELECT * FROM folders WHERE id = '" . _sql($parent_id) . "' LIMIT 1");

        // Nếu thư mục không tồn tại, thoát
        if (!$folder) {
            return null;
        }

        // Nếu thư mục có _byid = 0, trả về thư mục này (thư mục gốc cấp 1)
        if ($folder['_byid'] == 0) {
            return $folder; // Trả về thư mục gốc cấp 1
        }

        // Cập nhật parent_id cho lần lặp tiếp theo
        $parent_id = $folder['_byid'];
    }

    return null; // Nếu không tìm thấy thư mục gốc
}


function countFilesAndFolders($id) {
    // Đếm tổng số file và folder có cùng _byid
    $result = _fetch("
        SELECT 
            (SELECT COUNT(*) FROM files WHERE _tinhtrang='danghoatdong' and _byid = '" . _sql($id) . "') AS file_count,
            (SELECT COUNT(*) FROM folders WHERE _tinhtrang='danghoatdong' and _byid = '" . _sql($id) . "') AS folder_count
    ");

    // Tổng số file và folder
    return $result['file_count'] + $result['folder_count'];
}
