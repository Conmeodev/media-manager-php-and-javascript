
var cf_path = $("#cf_path").html();
var zIndexCounter = 100;


const lazyLoadStyles = document.createElement('style');
lazyLoadStyles.textContent = `
    img.ithumb {
        transition: opacity 400ms ease-out;
        opacity: 0;
    }
    img.ithumb.loaded {
        opacity: 1;
    }
`;
document.head.insertBefore(lazyLoadStyles, document.head.firstChild);

let observer;

function initLazyLoad() {
    observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                loadImage(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, {
        rootMargin: '200px 0px',
        threshold: 0.01
    });
}

function loadImage(img) {
    const realSrc = img.getAttribute('_src');
    if (!realSrc) return;

    const tempImg = new Image();
    tempImg.src = realSrc;
    
    tempImg.onload = () => {
        img.style.opacity = '0';
        img.src = realSrc;
        img.removeAttribute('_src');
        
        requestAnimationFrame(() => {
            img.style.transition = 'opacity 400ms ease-out';
            img.style.opacity = '1';
            img.classList.add('loaded');
        });
    };
    
    tempImg.onerror = () => {
        console.error('Lỗi tải ảnh:', realSrc);
        img.style.opacity = '1';
    };
}

function observeNewImages() {
    const newImages = document.querySelectorAll('img.ithumb[_src]:not([data-lazy-handled])');
    
    newImages.forEach(img => {
        img.setAttribute('data-lazy-handled', 'true');
        img.style.opacity = '0';
        
        const rect = img.getBoundingClientRect();
        const isVisible = (
            rect.top < window.innerHeight && 
            rect.bottom > 0 &&
            rect.left < window.innerWidth && 
            rect.right > 0
        );
        
        if (isVisible) {
            loadImage(img);
        } else if (observer) {
            observer.observe(img);
        }
    });
}

function startLazyLoad() {
    if ('IntersectionObserver' in window) {
        initLazyLoad();
    } else {
        console.warn('Trình duyệt không hỗ trợ IntersectionObserver');
    }
    
    observeNewImages();
    
    if (typeof MutationObserver !== 'undefined') {
        const domObserver = new MutationObserver(observeNewImages);
        domObserver.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', startLazyLoad);
} else {
    startLazyLoad();
}

function _alert(text,_time=3000,css='default-alert')  {
    zIndexCounter++;
    var alertId = `popup${zIndexCounter}`;
    $("body").prepend(`
        <div class="popup ${css}" id="${alertId}" style="z-index:${zIndexCounter}">${text}</div>
        `);

    setTimeout(function() {
        $(`#${alertId}`).fadeOut(function() {
            $(this).remove();
        });
    }, _time);

}
function _windows(title, code, width = 600, _css = 0, _only = 0, callback) {
    //$("body").css("overflow", "hidden");
    zIndexCounter++;

    var popupId = `popup${zIndexCounter}`;
    $("body").addClass("hide-scroll");
    $("body").prepend(`
        <div class="_view ${_css}" _only="${_only}" id="${popupId}" style="z-index:${zIndexCounter}">
            <div class="v_container" style="max-width:${width}px">
                <div class="v_header">
                    <span class="vh_name">${title}</span>
                    <div class="v_close">×</div>
                </div>
                <div class="v_body">
                    ${code}
                </div>
            </div>
        </div>
    `);

    var popup = $(`#${popupId}`);
    var container = popup.find('.v_container');
    
    // Thêm chức năng kéo di chuyển
    let isDragging = false;
    let offset = { x: 0, y: 0 };
    
    popup.find('.v_header').on('mousedown', function(e) {
        isDragging = true;
        offset.x = e.clientX - container.offset().left;
        offset.y = e.clientY - container.offset().top;
        container.css('transition', 'none');
    });
    
    $(document).on('mousemove', function(e) {
        if (!isDragging) return;
        
        container.css({
            'left': e.clientX - offset.x,
            'top': e.clientY - offset.y,
            'margin': '0',
            'transform': 'none',
            'position': 'fixed'
        });
    });
    
    $(document).on('mouseup', function() {
        isDragging = false;
        container.css('transition', 'all 0.3s ease-in-out');
    });

    openPopup(popup);

    function openPopup() {
        popup.css('display', 'flex');
        setTimeout(function() {
            popup.addClass('active');
        }, 10);
    }

    if (typeof callback === 'function') {
        callback();
    }

    function closePopup() {
        popup.removeClass('active');
        setTimeout(function() {
            popup.css('display', 'none');
            popup.remove();
            $("body").removeClass("hide-scroll");
        }, 300);
    }

    popup.find('.v_close').on("click", closePopup);

    return new Promise(resolve => {
        setTimeout(() => {
            resolve();
        }, 1000);
    });
}

function close_windows(el) {
    $(el).closest("._view").removeClass('active');
    setTimeout(() => {
        $(el).closest("._view").remove();
        $("body").removeClass("hide-scroll");
    }, 300);
}

function xwindows(name) {
    $(`[_only="${name}"]`).removeClass('active');
    setTimeout(() => {
        $(`[_only="${name}"]`).remove();
    }, 300);
}
function copy(element) {
  var copyText = document.getElementById(element);
  copyText.select();
  copyText.setSelectionRange(0, 99999);
  navigator.clipboard.writeText(copyText.value);
  _alert("Copy Thành Công: " + copyText.value);
}
function ajaxLove(id,type){
    var box = $("#loveid"+id);
    if(box.attr("love") != "love") {
        box.attr("love","love");
    } else {
        box.attr("love","none");
    }
    $.ajax({
        url: "/ajax/action.php",
        method: "POST",
        data: {act:"mylove",type:type,path:id},
        success: function(response) {
            res = $.parseJSON(response);
            _alert(res.title);
        }
    });
}
$(document).ready(function() {
    $(document).on('contextmenu', ".ajax_box", function(event) {
        event.preventDefault(); 
        _alert("Đang lấy nội dung", 2000);

        var box = $(this),
            name = htmlToBbcode(box.find(".list-name").text()),
            id = box.attr("_id"),
            _type = box.attr("_bbcode"),
            fileSize = _size(box.attr("_size")),
            loai = box.attr("_box"),
            link = box.attr("_dir"),
            thumb = box.find("img").attr("src");

        if (!link) link = "#";
        if (!thumb) thumb = "/default-thumbnail.jpg"; 

        var bbfile;
        if(_type == "video") {
        var bbfile = "[video]https://<?php echo $domain?>"+link+"[/video][URL=https://<?php echo $domain1?>/"+loai+"/"+ktdb(delBBcode(name))+"-<?php echo $domain ?>/"+id+"]"+delBBcode(name)+"- <?php echo $domain;?>[/URL]";
    } else if(_type == "image") {
        var bbfile = "[img]https://<?php echo $domain?>"+link+"[/img][URL=https://<?php echo $domain1?>/"+loai+"/"+ktdb(delBBcode(name))+"-<?php echo $domain ?>/"+id+"]"+delBBcode(name)+"- <?php echo $domain;?>[/URL]";
    } else if(loai == "folders"){
        var bbfile = "https://<?php echo $domain?>/"+loai+"/"+ktdb(delBBcode(name))+"/"+id;
    }

        _windows(name, `
            <div class="form" id="edit">
                <div class="none">
                    <input id="ed_id" value="${id}"> 
                    <input id="ed_type" value="${loai}"> 
                </div>
                <div class="group-input">
                    <div class="group-in">
                        <input type="text" id="rename" value="${name}" disabled>
                        <span class="btn center" id="btnRename" onclick="inputRename();">✎</span>
                    </div>
                    <div class="group-in">
                        <input type="text" id="url_select" value="<?php echo $http.$domain ?>/${_type}s/` + encodeURIComponent(name) + `/${id}" disabled>
                        <span class="btn center" onclick="copy('url_select');">❖</span>
                    </div>
                    <input id="bbcode-file" class="none" value="${bbfile}">
                    <a href="${thumb}" download class="btn">Download Thumb</a>
                    <a href="${link}" class="btn" id="download_file" download>Tải (${fileSize})</a>
                    <input id="bbcode-file" class="none" value="${bbfile}">
                    <span style="background-color: #f44336" class="btn" onclick="copy('bbcode-file');">Mã Nhúng</span>
                    <?php
                    if($admin) {?>
                    <hr>
                    <span class="btn" onclick="setThumb(${id},1)">Set Thumb</span>
                    <span class="btn" onclick="setThumb(${id},0)">Set Thumb root</span>
                    <span class="btn" onclick="delThumb(${id})">Delete Thumb</span>
                    <hr>
                    <span class="btn" style="background-color: #f44336" onclick="if (confirm('Bạn có chắc chắn muốn xoá file này không?')) del(${id}, 'files')"> Xoá File </span>
                    
                        <?php }?>
                    <hr>
                    <img style="max-width:100px" src="${thumb}">
                </div>
            </div>
        `);
    });
});

function del(path,type) {
    $.ajax({
        url: '/ajax/action.php',
        method: 'POST',
        data: {act:"move_trash",path:path,type:type},
        success:function(response) {
            console.log(response);
            var res = $.parseJSON(response);
            _alert(res.content);
        }
    });
}

function inputRename() {
    var input = $("#rename"),
        btn = $("#btnRename");

    if (input.prop('disabled')) {
        input.prop("disabled", false).focus();
        btn.html("√").css("background", "#47c336").attr("onclick", "rename();");
    }
}

function rename() {
    var form = $("#edit"),
        id = form.find("#ed_id").val(),
        loai = form.find("#ed_type").val(),
        newname = form.find("#rename").val(),
        btn = $("#btnRename"),
        input = $("#rename"),
        box = $("#" + loai + id);
        

    $.ajax({
        method: "POST",
        url: "/ajax/ajaxFile.php",
        data: { act: "rename", id: id, name: newname, type: loai },
        success: function(response) {
        console.log(response);
            var res = $.parseJSON(response);
            if (res.res === "true") {
                input.prop('disabled', true);
                btn.html("✎").css("background", "").attr("onclick", "inputRename()");
                box.find(".list-name").text(bbcodeToHtml(newname));
                _alert(res.content, 3000, res.res);
                $("[_id='" + id + "']").find(".list-name").html(newname);
            } else {
                _alert(res.content, 3000, res.res);
            }
        }
    });
}
/*
$(document).on('click', 'a[ajax]', function(e) {
    e.preventDefault();
    var $link = $(this);
    var url = $link.attr('href');
    var $targetContainer = $('.right .container .morefile'); 
    
    if (!$link.find('.viewed-marker').length) {
        $link.append('<span class="viewed-marker"> 📌</span>');
    }
    
    $targetContainer.addClass('loading').fadeTo(200, 0.5);
    
    $('html, body').animate({
        scrollTop: $targetContainer.offset().top - 20
    }, 500);
    
    $.ajax({
        url: url,
        success: function(response) {
            var $responseDoc = $('<div>').html(response);
            var newContent = $responseDoc.find('.right .container .morefile').html();
            var newHead = $responseDoc.filter('head').html();
            var newTitle = $responseDoc.filter('title').text();
            
            $('head').html(newHead);
            $targetContainer.html(newContent).fadeTo(300, 1).removeClass('loading');
            document.title = newTitle;
            
            history.pushState({url: url}, newTitle, url);
            
            $targetContainer.find('script').each(function() {
                $.globalEval(this.text || this.textContent || this.innerHTML || '');
            });
        },
        error: function(xhr) {
            console.error('AJAX Error:', xhr.statusText);
            $targetContainer.removeClass('loading').fadeTo(300, 1);
            window.location.href = url; // Fallback
        }
    });
});
*/

$(document).on('click', 'a[ajax]', function(e) {
    let vid = document.getElementById("myVideoView");
    if (vid) {
        vid.pause();
    }
    e.preventDefault();
    var $link = $(this);
    var url = $link.attr('href');
    var bbcode = $link.attr('_bbcode');
    var thumb = $link.attr('_thumb');
    var dir = $link.attr('_dir');
    var title = '<a href="'+url+'" style="color:white">Đi tới: '+$link.attr('_name')+'</a>';
    var name = $link.attr('_name');

    if (!$link.find('.viewed-marker').length) {
        $link.append('<span class="viewed-marker"> 📌</span>');
    }

    var initialContent = '';
    if (bbcode === 'video') {
        initialContent = `
        <div class="view-post">
            <div class="background-view" style="background-image: url('${thumb}');"></div>
            <div class="main-view">
                <video controls loop autoplay loading="lazy">
                    <source src="${dir}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
        <div id="ajax-content" class="menu"></div>`;
    } 
    else if (bbcode === 'image') {
        initialContent = `
        <div class="view-post">
            <div class="background-view" style="background-image: url('${thumb}');"></div>
            <div class="main-view">
                <img src="${dir}" loading="lazy" alt="${name}">
            </div>
        </div>
        <div id="ajax-content" class="menu" style="margin:0"></div>`;
    }

    _windows(title, initialContent, 800, 0, 'ajax-window');

    $.ajax({
        url: url,
        success: function(response) {
            var $responseDoc = $('<div>').html(response);
            var newContent = $responseDoc.find('.right .container .morefile').html();
            
            $('#ajax-content').html(newContent);
            
            $('#ajax-content script').each(function() {
                $.globalEval(this.text || this.textContent || this.innerHTML || '');
            });
        },
        error: function(xhr) {
            console.error('AJAX Error:', xhr.statusText);
            $('#ajax-content').html('<div class="error-message">Lỗi khi tải nội dung bổ sung.</div>');
        }
    });
});


$(document).ready(function() {
    $('head').append(`
        <style>
            .loading-spinner {
                text-align: center;
                padding: 50px;
                color: #333;
                font-size: 16px;
            }
            .error-message {
                text-align: center;
                padding: 50px;
                color: #d9534f;
            }
            .error-message a {
                color: #0078d7;
                text-decoration: underline;
            }
        </style>
    `);
});


/*
// Xử lý popstate
$(window).on('popstate', function(e) {
    if (e.originalEvent.state?.url) {
        var $target = $('a[ajax][href="' + e.originalEvent.state.url + '"]');
        if ($target.length) {
            $target.trigger('click');
        } else {
            window.location.href = e.originalEvent.state.url;
        }
    }
});
*/

//// rand post

function randPost() {
    var url = "/ajax/view_files.php";
    var $randPost = $('.class_rand_post');
    $randPost.addClass('loading').fadeTo(200, 0.5);
    $.ajax({
        url: url,
        success: function(response) {
            $randPost.append(response).fadeTo(300, 1).removeClass('loading');
        },
        error: function(xhr) {
            console.error('AJAX Error:', xhr.statusText);
            $randPost.removeClass('loading').fadeTo(300, 1);
            window.location.href = url; // Fallback
        }
    });
}
$(window).scroll(function () {
    var $target = $('.block.class_rand_post');

    if ($target.length) {
        var targetBottom = $target.offset().top + $target.outerHeight();
        var windowBottom = $(window).scrollTop() + $(window).height();

        if (windowBottom >= targetBottom - 100) {
            randPost();
        }
    }
});
