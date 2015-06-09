function singleDateCheck(month) {
    if (month < 10) {
        return '0' + month;
    }

    return month;
}

function dateToZhcn(date) {
    date = date.replace("-", "年");
    date = date.replace("-", "月");

    return date + "日";
}