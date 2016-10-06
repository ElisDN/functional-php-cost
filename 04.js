
var a = 5;

function b(c) {
    return c + a;
}

console.info(b(3));

console.info('----');

function d() {
    var e = 6;

    function f(g) {
        return g + e;
    }

    console.info(f(5));
}

d();

console.info('----');

function h(i) {

    function j(k) {
        return k + i;
    }

    console.info(j(5));
}

h(7);

console.info('----');

function l(m) {

    function o(n) {
        return n + m;
    }

    return o;
}

var p = l(3); console.info(p(7));
var r = l(4); console.info(r(7));

console.info(p);

console.info('----');

function s(t) {
    return function (u) {
        return u + t;
    };
}

var v = s(9); console.info(v(7));