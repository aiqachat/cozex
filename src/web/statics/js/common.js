/**
 * 浏览器跳转链接
 * @param {JSON} params
 * @param {bool} newWindow
 */
const navigateTo = (params, newWindow = false) => {
    let url = null;
    if (typeof params === 'string') {
        url = params;
    } else {
        const queryString = Qs.stringify(params);
        url = `${_scriptUrl}?${queryString}`;
    }
    if (newWindow) {
        window.open(url);
    } else {
        window.location.href = url;
    }
};

const historyGo = (number) => {
    if (typeof number === 'number') {
        window.history.go(number);
    }
};

const Navigate = {
    install(Vue, options) {
        Vue.prototype.$navigate = function (params, newWindow) {
            navigateTo(params, newWindow);
        }
    }
};

const HistoryGo = {
    install(Vue, options) {
        Vue.prototype.$historyGo = function (number) {
            historyGo(number);
        }
    }
};

Vue.use(Navigate);
Vue.use(HistoryGo);

/**
 * 获取get请求参数的值
 * @param {String} name
 * @returns {String||null}
 */
const getQuery = (name) => {
    const reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    const r = window.location.search.substr(1).match(reg);
    if (r != null) {
        return decodeURIComponent(r[2]);
    }
    return null;
};

const getAllUrlParams = (url) => {
    // 用JS拿到URL，如果函数接收了URL，那就用函数的参数。如果没传参，就使用当前页面的URL
    var queryString = url ? url.split('?')[1] : window.location.search.slice(1);
    // 用来存储我们所有的参数
    var obj = {};
    // 如果没有传参，返回一个空对象
    if (!queryString) {
        return obj;
    }
    // stuff after # is not part of query string, so get rid of it
    queryString = queryString.split('#')[0];
    // 将参数分成数组
    var arr = queryString.split('&');
    for (var i = 0; i < arr.length; i++) {
        // 分离成key:value的形式
        var a = arr[i].split('=');
        // 将undefined标记为true
        var paramName = a[0];
        var paramValue = typeof (a[1]) === 'undefined' ? true : a[1];
        // 如果调用对象时要求大小写区分，可删除这两行代码
        paramName = paramName.toLowerCase();
        if (typeof paramValue === 'string') paramValue = paramValue.toLowerCase();
        // 如果paramName以方括号结束, e.g. colors[] or colors[2]
        if (paramName.match(/\[(\d+)?\]$/)) {
            // 如果paramName不存在，则创建key
            var key = paramName.replace(/\[(\d+)?\]/, '');
            if (!obj[key]) obj[key] = [];
            // 如果是索引数组 e.g. colors[2]
            if (paramName.match(/\[\d+\]$/)) {
                // 获取索引值并在对应的位置添加值
                var index = /\[(\d+)\]/.exec(paramName)[1];
                obj[key][index] = paramValue;
            } else {
                // 如果是其它的类型，也放到数组中
                obj[key].push(paramValue);
            }
        } else {
            // 处理字符串类型
            if (!obj[paramName]) {
                // 如果如果paramName不存在，则创建对象的属性
                obj[paramName] = paramValue;
            } else if (obj[paramName] && typeof obj[paramName] === 'string') {
                // 如果属性存在，并且是个字符串，那么就转换为数组
                obj[paramName] = [obj[paramName]];
                obj[paramName].push(paramValue);
            } else {
                // 如果是其它的类型，还是往数组里丢
                obj[paramName].push(paramValue);
            }
        }
    }
    return obj;
};


/**
 * 获取cookie值
 * @param {String} cname
 * @returns {String||null}
 */
const getCookieValue = (cname) => {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

/**
 * 生成随机字符串
 * @param {Number} len
 * @returns {string}
 */
const randomString = (len) => {
    len = len || 32;
    let $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
    /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
    let maxPos = $chars.length;
    let pwd = '';
    for (i = 0; i < len; i++) {
        pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return pwd;
};

const common = axios.create({
    transformRequest: [function (data, headers) {
        if (data instanceof FormData) {
            data.append('_csrf', _csrf);
        } else {
            if (data && !data['_csrf']) {
                data['_csrf'] = _csrf;
            }
            data = Qs.stringify(data);
        }
        return data;
    }],
});

window.request = common;

common.defaults.baseURL = _scriptUrl;
common.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
common.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

common.interceptors.request.use(function (config) {
    return config;
}, function (error) {
    return Promise.reject(error);
});

common.interceptors.response.use(function (response) {
    if (response.data && typeof response.data.code !== 'undefined') {
        if (response.data.code >= 400) {
            if (_layout) {
                _layout.$alert(response.data.msg, '错误');
            } else {
                console.log(response.data);
            }
        } else {
            return response;
        }
    } else {
        return Promise.reject(response);
    }
}, function (error) {
    if (_layout) {
        _layout.$alert(response.data.msg, '错误');
    } else {
        console.log(response.data);
    }
    return Promise.reject(error);
});

Vue.use({
    install(Vue, options) {
        Vue.prototype.$request = request;
    }
});

// 传入请求地址与页数获取列表
const loadList = (url, page) => {
    return request({
        params: {
            r: url,
            page: page
        },
    }).then(e => {
        if (e.data.code === 0) {
            return e.data.data;
        } else {
            this.$message.error(e.data.msg);
        }
    }).catch(e => {
    });
};