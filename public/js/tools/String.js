function singleDateCheck(month) {
    if (month < 10) {
        return '0' + month;
    }

    return month;
}