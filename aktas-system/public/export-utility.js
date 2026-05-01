/**
 * Data Export Utility
 * Provides CSV, PDF, Excel, and JSON export functionality
 */

class DataExporter {
    constructor() {
        this.dateFormat = 'YYYY-MM-DD';
        this.timestampFormat = 'YYYY-MM-DD HH:mm:ss';
    }

    /**
     * Export table to CSV format
     * @param {string} tableSelector - CSS selector for table
     * @param {string} filename - Output filename
     * @param {object} options - Export options
     */
    exportTableToCSV(tableSelector, filename = 'export.csv', options = {}) {
        const table = document.querySelector(tableSelector);
        if (!table) {
            console.error('Table not found:', tableSelector);
            return;
        }

        let csv = [];
        const includeHeaders = options.includeHeaders !== false;
        const dateColumn = options.dateColumn || null;

        // Get headers
        if (includeHeaders) {
            const headers = Array.from(table.querySelectorAll('thead th'))
                .map(th => this.escapeCSV(th.textContent.trim()));
            csv.push(headers.join(','));
        }

        // Get rows
        Array.from(table.querySelectorAll('tbody tr')).forEach((row, rowIndex) => {
            if (options.skipRows && options.skipRows.includes(rowIndex)) return;

            const cells = Array.from(row.querySelectorAll('td'))
                .map((td, colIndex) => {
                    let text = td.textContent.trim();
                    
                    // Remove badge/badge HTML
                    text = text.replace(/<[^>]*>/g, '');
                    
                    return this.escapeCSV(text);
                });
            csv.push(cells.join(','));
        });

        // Add summary if provided
        if (options.summary) {
            csv.push('');
            csv.push(options.summary);
        }

        this.downloadFile(csv.join('\n'), filename, 'text/csv');
        console.log('CSV exported:', filename);
    }

    /**
     * Export table to PDF format
     * Requires html2pdf library
     * @param {string} tableSelector - CSS selector for table
     * @param {string} title - PDF title
     * @param {object} options - Export options
     */
    exportTableToPDF(tableSelector, title = 'Export Report', options = {}) {
        const element = document.querySelector(tableSelector);
        if (!element) {
            console.error('Table not found:', tableSelector);
            return;
        }

        if (typeof html2pdf === 'undefined') {
            console.error('html2pdf library not loaded');
            alert('PDF export requires html2pdf library');
            return;
        }

        const opt = {
            margin: options.margin || 10,
            filename: (title + '.pdf').replace(/\s+/g, '_'),
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: {
                orientation: options.orientation || 'landscape',
                unit: 'mm',
                format: 'a4'
            }
        };

        const cloneElement = element.cloneNode(true);
        
        // Add title
        if (title) {
            const titleDiv = document.createElement('div');
            titleDiv.style.fontSize = '18px';
            titleDiv.style.fontWeight = 'bold';
            titleDiv.style.marginBottom = '10px';
            titleDiv.textContent = title;
            cloneElement.prepend(titleDiv);
        }

        // Add timestamp
        if (options.includeTimestamp !== false) {
            const timestampDiv = document.createElement('div');
            timestampDiv.style.fontSize = '12px';
            timestampDiv.style.color = '#666';
            timestampDiv.style.marginBottom = '10px';
            timestampDiv.textContent = `Generated on: ${new Date().toLocaleString()}`;
            cloneElement.append(timestampDiv);
        }

        html2pdf().set(opt).from(cloneElement).save();
        console.log('PDF exported:', opt.filename);
    }

    /**
     * Export table to Excel format
     * Requires SheetJS library
     * @param {string} tableSelector - CSS selector for table
     * @param {string} filename - Output filename
     * @param {object} options - Export options
     */
    exportTableToExcel(tableSelector, filename = 'export.xlsx', options = {}) {
        const table = document.querySelector(tableSelector);
        if (!table) {
            console.error('Table not found:', tableSelector);
            return;
        }

        if (typeof XLSX === 'undefined') {
            console.error('SheetJS library not loaded');
            alert('Excel export requires SheetJS library');
            return;
        }

        const data = [];
        const sheetName = options.sheetName || 'Export';

        // Get headers
        const headers = Array.from(table.querySelectorAll('thead th'))
            .map(th => th.textContent.trim());
        data.push(headers);

        // Get rows
        Array.from(table.querySelectorAll('tbody tr')).forEach(row => {
            const cells = Array.from(row.querySelectorAll('td'))
                .map(td => {
                    let text = td.textContent.trim();
                    // Remove HTML tags
                    text = text.replace(/<[^>]*>/g, '');
                    return text;
                });
            data.push(cells);
        });

        // Create workbook
        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, sheetName);

        // Adjust column widths
        const colWidths = headers.map(h => ({ wch: Math.max(h.length + 2, 12) }));
        ws['!cols'] = colWidths;

        // Save file
        XLSX.writeFile(wb, filename);
        console.log('Excel exported:', filename);
    }

    /**
     * Export table to JSON format
     * @param {string} tableSelector - CSS selector for table
     * @param {string} filename - Output filename
     * @param {object} options - Export options
     */
    exportTableToJSON(tableSelector, filename = 'export.json', options = {}) {
        const table = document.querySelector(tableSelector);
        if (!table) {
            console.error('Table not found:', tableSelector);
            return;
        }

        const data = [];

        // Get headers
        const headers = Array.from(table.querySelectorAll('thead th'))
            .map(th => th.textContent.trim().toLowerCase().replace(/\s+/g, '_'));

        // Get rows
        Array.from(table.querySelectorAll('tbody tr')).forEach(row => {
            const obj = {};
            Array.from(row.querySelectorAll('td')).forEach((td, index) => {
                let value = td.textContent.trim();
                value = value.replace(/<[^>]*>/g, '');
                obj[headers[index]] = value;
            });
            data.push(obj);
        });

        const json = JSON.stringify(data, null, 2);
        this.downloadFile(json, filename, 'application/json');
        console.log('JSON exported:', filename);
    }

    /**
     * Export filtered data with date range
     * @param {array} data - Array of data objects
     * @param {string} filename - Output filename
     * @param {object} options - Export options
     */
    exportArrayData(data, filename = 'export.csv', options = {}) {
        const format = options.format || 'csv';  // csv, json, excel

        // Filter by date range if provided
        if (options.dateFrom && options.dateTo && options.dateField) {
            data = this.filterByDateRange(data, options.dateField, options.dateFrom, options.dateTo);
        }

        // Filter by column values if provided
        if (options.filters) {
            data = this.filterByColumns(data, options.filters);
        }

        if (format === 'csv') {
            this.arrayToCSV(data, filename, options);
        } else if (format === 'json') {
            this.arrayToJSON(data, filename);
        } else if (format === 'excel') {
            this.arrayToExcel(data, filename, options);
        }
    }

    /**
     * Convert array of objects to CSV and download
     */
    arrayToCSV(data, filename, options = {}) {
        if (data.length === 0) {
            console.warn('No data to export');
            return;
        }

        const headers = options.headers || Object.keys(data[0]);
        const csv = [];

        // Add headers
        csv.push(headers.map(h => this.escapeCSV(h)).join(','));

        // Add rows
        data.forEach(row => {
            const values = headers.map(header => {
                const value = row[header] || '';
                return this.escapeCSV(String(value));
            });
            csv.push(values.join(','));
        });

        // Add summary/totals if provided
        if (options.totals) {
            csv.push('');
            csv.push(options.totals);
        }

        this.downloadFile(csv.join('\n'), filename, 'text/csv');
    }

    /**
     * Convert array of objects to JSON and download
     */
    arrayToJSON(data, filename) {
        const json = JSON.stringify(data, null, 2);
        this.downloadFile(json, filename, 'application/json');
    }

    /**
     * Convert array of objects to Excel and download
     */
    arrayToExcel(data, filename, options = {}) {
        if (typeof XLSX === 'undefined') {
            alert('Excel export requires SheetJS library');
            return;
        }

        const headers = options.headers || Object.keys(data[0] || {});
        const rows = [headers];

        data.forEach(row => {
            rows.push(headers.map(h => row[h] || ''));
        });

        const ws = XLSX.utils.aoa_to_sheet(rows);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, options.sheetName || 'Data');

        XLSX.writeFile(wb, filename);
    }

    /**
     * Generate report with custom headers and formatting
     * @param {array} data - Data to export
     * @param {object} reportConfig - Report configuration
     * @param {string} filename - Output filename
     */
    generateReport(data, reportConfig, filename = 'report.csv') {
        let csv = [];

        // Add report header
        if (reportConfig.title) {
            csv.push(reportConfig.title);
            csv.push(reportConfig.subtitle || '');
            csv.push(`Generated: ${new Date().toLocaleString()}`);
            csv.push('');
        }

        // Add table data
        const headers = reportConfig.headers || Object.keys(data[0] || {});
        csv.push(headers.join(','));

        data.forEach(row => {
            const values = headers.map(h => this.escapeCSV(String(row[h] || '')));
            csv.push(values.join(','));
        });

        // Add summary/totals
        if (reportConfig.totals) {
            csv.push('');
            csv.push('SUMMARY');
            Object.entries(reportConfig.totals).forEach(([key, value]) => {
                csv.push(`${key},${value}`);
            });
        }

        // Add footer
        if (reportConfig.footer) {
            csv.push('');
            csv.push(reportConfig.footer);
        }

        this.downloadFile(csv.join('\n'), filename, 'text/csv');
    }

    /**
     * Filter data by date range
     */
    filterByDateRange(data, dateField, dateFrom, dateTo) {
        const from = new Date(dateFrom);
        const to = new Date(dateTo);

        return data.filter(item => {
            const itemDate = new Date(item[dateField]);
            return itemDate >= from && itemDate <= to;
        });
    }

    /**
     * Filter data by column values
     */
    filterByColumns(data, filters) {
        return data.filter(item => {
            return Object.entries(filters).every(([column, value]) => {
                if (Array.isArray(value)) {
                    return value.includes(item[column]);
                }
                return item[column] == value;
            });
        });
    }

    /**
     * Escape special characters for CSV
     */
    escapeCSV(text) {
        text = String(text || '').trim();
        if (text.includes(',') || text.includes('"') || text.includes('\n')) {
            text = `"${text.replace(/"/g, '""')}"`;
        }
        return text;
    }

    /**
     * Download file
     */
    downloadFile(content, filename, mimeType) {
        const blob = new Blob([content], { type: mimeType });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    }

    /**
     * Create export buttons for a table
     */
    createExportButtons(tableSelector, baseFilename = 'export') {
        const container = document.createElement('div');
        container.className = 'export-button-group';
        container.style.marginBottom = '15px';

        const formats = [
            { format: 'csv', label: 'CSV', icon: 'bi-filetype-csv', method: this.exportTableToCSV.bind(this) },
            { format: 'json', label: 'JSON', icon: 'bi-code-curly', method: this.exportTableToJSON.bind(this) },
            { format: 'pdf', label: 'PDF', icon: 'bi-file-earmark-pdf', method: this.exportTableToPDF.bind(this) }
        ];

        formats.forEach(({ format, label, icon, method }) => {
            const button = document.createElement('button');
            button.className = 'btn btn-outline-primary btn-sm';
            button.style.marginRight = '8px';
            button.innerHTML = `<i class="bi ${icon}"></i> ${label}`;
            button.onclick = () => {
                method(tableSelector, `${baseFilename}.${format === 'pdf' ? 'pdf' : format}`);
            };
            container.appendChild(button);
        });

        return container;
    }
}

// Global instance
const dataExporter = new DataExporter();

/**
 * Quick export function for tables
 */
function quickExport(tableSelector, format = 'csv', filename = 'export') {
    const fullFilename = `${filename}.${format}`;
    
    if (format === 'csv') {
        dataExporter.exportTableToCSV(tableSelector, fullFilename);
    } else if (format === 'json') {
        dataExporter.exportTableToJSON(tableSelector, fullFilename);
    } else if (format === 'pdf') {
        dataExporter.exportTableToPDF(tableSelector, filename);
    } else if (format === 'excel') {
        dataExporter.exportTableToExcel(tableSelector, fullFilename);
    }
}

/**
 * Generate sales report
 */
function generateSalesReport(salesData, filename = 'sales_report.csv') {
    const report = {
        title: 'Sales Report',
        subtitle: `Period: ${new Date().toLocaleDateString()}`,
        headers: ['Date', 'Employee', 'Amount', 'Items', 'Status'],
        totals: {
            'Total Sales': salesData.reduce((sum, s) => sum + parseFloat(s.amount || 0), 0).toFixed(2),
            'Total Items': salesData.reduce((sum, s) => sum + parseInt(s.quantity || 0), 0),
            'Number of Transactions': salesData.length
        }
    };

    dataExporter.generateReport(salesData, report, filename);
}

/**
 * Generate commission report
 */
function generateCommissionReport(employees, filename = 'commission_report.csv') {
    const report = {
        title: 'Commission Report',
        subtitle: `Generated: ${new Date().toLocaleDateString()}`,
        headers: ['Employee', 'Base Salary', 'Commission Rate', 'Total Sales', 'Commission Earned'],
        totals: {
            'Total Salaries': employees.reduce((sum, e) => sum + parseFloat(e.base_salary || 0), 0).toFixed(2),
            'Total Commission': employees.reduce((sum, e) => sum + parseFloat(e.commission_earned || 0), 0).toFixed(2)
        }
    };

    dataExporter.generateReport(employees, report, filename);
}

/**
 * Generate inventory report
 */
function generateInventoryReport(products, filename = 'inventory_report.csv') {
    const report = {
        title: 'Inventory Report',
        subtitle: `As of: ${new Date().toLocaleString()}`,
        headers: ['Product Name', 'SKU', 'Category', 'Stock Quantity', 'Unit Price', 'Stock Value', 'Status'],
        totals: {
            'Total Items': products.length,
            'Total Stock Value': products.reduce((sum, p) => sum + (parseFloat(p.stock_quantity || 0) * parseFloat(p.unit_price || 0)), 0).toFixed(2)
        }
    };

    dataExporter.generateReport(products, report, filename);
}
