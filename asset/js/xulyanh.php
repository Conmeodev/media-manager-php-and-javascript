
$(document).ready(function () {
    let hangDoiAnh = [];
    let dangXuLy = false;

    function xuLyAnhTiepTheo() {
        if (hangDoiAnh.length > 0 && !dangXuLy) {
            dangXuLy = true;
            let phanTu = hangDoiAnh.shift();
            let img = phanTu.find(".ithumb");
            let fileId = phanTu.attr("_id");
            let videoLink = phanTu.attr("_dir");
            let token = phanTu.attr("_token");

            if (!videoLink || !fileId || !token) {
                console.error("Thiếu thông tin:", { videoLink, fileId, token });
                ketThucXuLy();
                return;
            }

            img.attr("src", "/asset/image/loading.gif");

            $.post("<?php echo $u_thumb; ?>", { id: fileId, act: "get" }, function (res) {
                let path = extractThumbPath(res);
                if (path) {
                    img.attr("src", path);
                    ketThucXuLy();
                } else {
                    taoAnhMoiTuVideo(videoLink, img, fileId, token);
                }
            }).fail(function (jqXHR, textStatus) {
                console.error("AJAX lỗi:", textStatus);
                ketThucXuLy();
            });
        }
    }

    function taoAnhMoiTuVideo(videoLink, img, fileId, token) {
        let video = document.createElement("video");
        video.src = videoLink;
        video.crossOrigin = "anonymous";
        video.preload = "auto";

        video.addEventListener("loadedmetadata", () => {
            video.currentTime = Math.min(0.1, video.duration);
        });

        video.addEventListener("seeked", () => {
            setTimeout(() => {
                let canvas = document.createElement("canvas");
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                let ctx = canvas.getContext("2d");
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                let duLieuAnhMoi = canvas.toDataURL("image/png");

                if (duLieuAnhMoi.startsWith("data:image")) {
                    $.post("<?php echo $u_thumb; ?>", {
                        act: "thumb",
                        id: fileId,
                        token: token,
                        imgData: duLieuAnhMoi
                    }, function (res) {
                        let path = extractThumbPath(res);
                        if (path) {
                            img.attr("src", path);
                        } else {
                            console.error("Server trả về dữ liệu không hợp lệ:", res);
                        }
                        ketThucXuLy();
                    }).fail(() => {
                        console.error("Gửi ảnh lên server lỗi");
                        ketThucXuLy();
                    });
                } else {
                    console.error("Không tạo được ảnh từ video");
                    ketThucXuLy();
                }
            }, 100);
        });

        video.onerror = () => {
            console.error("Không tải được video:", videoLink);
            ketThucXuLy();
        };
    }

    function extractThumbPath(text) {
        if (!text) return null;
        if (text.startsWith("/") || text.startsWith("http")) return text.trim();
        let match = text.match(/\/_thumbs\/[^\s]+\.png/);
        return match ? match[0] : null;
    }

    function ketThucXuLy() {
        dangXuLy = false;
        xuLyAnhTiepTheo();
    }

    function kiemTraPhanTuTrongKhungNhin(selector) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    let anh = $(entry.target);
                    if (!anh.data("processed")) {
                        let thumb = anh.attr("_thumb");
                        let img = anh.find(".ithumb");
                        if (thumb && (thumb.startsWith("/") || thumb.startsWith("http"))) {
                            img.attr("src", thumb);
                        } else {
                            hangDoiAnh.push(anh);
                            xuLyAnhTiepTheo();
                        }
                        anh.data("processed", true);
                    }
                }
            });
        }, { threshold: 0.2 });

        $(selector).each(function () {
            observer.observe(this);
        });
    }

    kiemTraPhanTuTrongKhungNhin(".loadthumb");
});
