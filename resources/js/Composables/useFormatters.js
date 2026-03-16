export function useFormatters() {
    const formatCurrency = (value) => {
        if (value == null) return '0đ';
        return new Intl.NumberFormat('vi-VN').format(Number(value)) + 'đ';
    };

    const formatDate = (date) => {
        if (!date) return '';
        return new Date(date).toLocaleDateString('vi-VN', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    return { formatCurrency, formatDate };
}
