
function htmlToBbcode(html)  {
    const searchReplace = [
        { search: /<span style="background:(.*?);display:block;width:100%;height:1px;margin-top: 11px;"><\/span>/gi, replace: '[hr=$1]' },
        { search: /<span style="color:(.*?)">(.*?)<\/span>/gi, replace: '[color=$1]$2[/color]' },
        { search: /<div style="(.*?)">(.*?)<\/div>/gi, replace: '[div=$1]$2[/div]' },
        { search: /<strong>(.*?)<\/strong>/gi, replace: '[b]$1[/b]' },
        { search: /<a href="(.*?)">(.*?)<\/a>/gi, replace: (match, p1, p2) => p1 === p2 ? `[url]${p1}[/url]` : `[url=${p1}]${p2}[/url]` },
        { search: /<img src="(.*?)">/gi, replace: '[img]$1[/img]' }
    ];

    searchReplace.forEach(pair => {
        html = html.replace(pair.search, pair.replace);
    });

    return html;
}
function bbcodeToHtml(bbcode) {
    // List of search and replace patterns
    const searchReplace = [
        { search: /\[hr=(.*?)\]/gi, replace: '<span style="background:$1;display:block;width:100%;height:1px;margin-top: 11px;"></span>' },
        { search: /\[color=(.*?)\](.*?)\[\/color\]/gi, replace: '<span style="color:$1">$2</span>' },
        { search: /\[div=(.*?)\](.*?)\[\/div\]/gi, replace: '<div style="$1">$2</div>' },
        { search: /\[b\](.*?)\[\/b\]/gi, replace: '<strong>$1</strong>' },
        { search: /\[url\](.*?)\[\/url\]/gi, replace: '<a href="$1">$1</a>' },
        { search: /\[url=(.*?)\](.*?)\[\/url\]/gi, replace: '<a href="$1">$2</a>' },
        { search: /\[img\](.*?)\[\/img\]/gi, replace: '<img src="$1">' }
    ];

    // Apply each replacement
    searchReplace.forEach(pair => {
        bbcode = bbcode.replace(pair.search, pair.replace);
    });

    return bbcode;
}


function delBBcode(input) {
    return input.replace(/\[.*?\]/g, '');
}
function ktdb(input) {
    const from = "áàảãạăắằẳẵặâấầẩẫậéèẻẽẹêếềểễệóòỏõọôốồổỗộơớờởỡợúùủũụưứừửữựíìỉĩịýỳỷỹỵđ";
    const to   = "aaaaaaaaaaaaaaaaaeeeeeeeeeeeooooooooooooooooouuuuuuuuuuuiiiiiyyyyyd";
    let result = input.toLowerCase();
    for (let i = 0; i < from.length; i++) {
        result = result.replace(new RegExp(from[i], 'g'), to[i]);
    }

    result = result.replace(/[^a-z0-9\s-]/g, '');

    result = result.replace(/\s+/g, '-'); 

    result = result.replace(/^-+|-+$/g, '');

    return result;
}



function _size(size) {
    if (size < 1024) {
        return size + ' B'; // Byte
    } else if (size < 1048576) { // 1024 * 1024
        return (size / 1024).toFixed(2) + ' KB';
    } else if (size < 1073741824) { // 1024 * 1024 * 1024
        return (size / 1048576).toFixed(2) + ' MB';
    } else if (size < 1099511627776) { // 1024 * 1024 * 1024 * 1024
        return (size / 1073741824).toFixed(2) + ' GB';
    } else {
        return (size / 1099511627776).toFixed(2) + ' TB';
    }
}
