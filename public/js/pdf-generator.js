/**
 * PDF Generator for Reports
 * Uses jsPDF library to generate PDFs on the client side
 */

class ReportPDFGenerator {
    constructor() {
        // jsPDF is now preloaded, no need to load dynamically
        this.isReady = typeof window.jsPDF !== 'undefined';
        if (!this.isReady) {
            console.warn('jsPDF not loaded. PDF generation may be slower.');
        }
    }

    /**
     * Generate PDF from report data (optimized for speed)
     */
    generatePDF(reportData, reportType, includeCharts = true) {
        return new Promise((resolve, reject) => {
            try {
                if (this.isReady) {
                    // jsPDF is already loaded, generate immediately
                    this.createPDF(reportData, reportType, includeCharts);
                    resolve();
                } else {
                    // Fallback: wait for jsPDF to load
                    const checkJsPDF = () => {
                        if (typeof window.jsPDF !== 'undefined') {
                            this.createPDF(reportData, reportType, includeCharts);
                            resolve();
                        } else {
                            setTimeout(checkJsPDF, 50); // Reduced from 100ms to 50ms
                        }
                    };
                    checkJsPDF();
                }
            } catch (error) {
                reject(error);
            }
        });
    }

    /**
     * Create the actual PDF (optimized for speed)
     */
    createPDF(reportData, reportType, includeCharts) {
        const { jsPDF } = window.jsPDF;
        const doc = new jsPDF();

        // PDF settings (cached for performance)
        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();
        const margin = 20;
        let yPosition = margin;

        // Pre-defined colors (avoid repeated array creation)
        const primaryColor = [30, 86, 49]; // #1e5631
        const secondaryColor = [102, 102, 102]; // #666
        const lightGray = [248, 249, 250]; // #f8f9fa

        // Optimize data processing - limit records for faster rendering
        const maxRecords = 50; // Limit to 50 records for faster PDF generation
        const limitedData = reportData.data ? reportData.data.slice(0, maxRecords) : [];

        // Header
        doc.setFontSize(20);
        doc.setTextColor(...primaryColor);
        doc.text(reportData.title || 'Report', pageWidth / 2, yPosition, { align: 'center' });
        yPosition += 10;

        doc.setFontSize(12);
        doc.setTextColor(...secondaryColor);
        doc.text('Scholarship Management System', pageWidth / 2, yPosition, { align: 'center' });
        yPosition += 8;

        doc.setFontSize(10);
        doc.text(`Generated on ${new Date().toLocaleDateString()}`, pageWidth / 2, yPosition, { align: 'center' });
        yPosition += 20;

        // Summary Section
        if (reportData.summary) {
            doc.setFontSize(14);
            doc.setTextColor(...primaryColor);
            doc.text('Report Summary', margin, yPosition);
            yPosition += 10;

            doc.setFontSize(10);
            doc.setTextColor(0, 0, 0);

            const summaryData = reportData.summary;
            let xPos = margin;
            const boxWidth = (pageWidth - 2 * margin) / 3;

            Object.entries(summaryData).forEach(([key, value], index) => {
                if (index > 0 && index % 3 === 0) {
                    yPosition += 25;
                    xPos = margin;
                }

                // Draw summary box
                doc.setFillColor(...lightGray);
                doc.rect(xPos, yPosition - 5, boxWidth - 5, 20, 'F');

                doc.setFontSize(16);
                doc.setTextColor(...primaryColor);
                doc.text(value.toString(), xPos + 5, yPosition + 5);

                doc.setFontSize(8);
                doc.setTextColor(...secondaryColor);
                doc.text(this.formatLabel(key), xPos + 5, yPosition + 12);

                xPos += boxWidth;
            });

            yPosition += 35;
        }

        // Charts Section (if enabled)
        if (includeCharts && reportData.chartData) {
            doc.setFontSize(14);
            doc.setTextColor(...primaryColor);
            doc.text('Data Visualization', margin, yPosition);
            yPosition += 15;

            // Chart placeholder and data
            if (reportData.chartData.by_scholarship_type) {
                doc.setFontSize(12);
                doc.setTextColor(0, 0, 0);
                doc.text('Scholarship Types Distribution:', margin, yPosition);
                yPosition += 10;

                let xPos = margin;
                Object.entries(reportData.chartData.by_scholarship_type).forEach(([type, count]) => {
                    doc.setFontSize(10);
                    doc.text(`${this.capitalizeFirst(type)}: ${count}`, xPos, yPosition);
                    xPos += 60;
                    if (xPos > pageWidth - 60) {
                        xPos = margin;
                        yPosition += 8;
                    }
                });
                yPosition += 15;
            }

            if (reportData.chartData.by_status) {
                doc.setFontSize(12);
                doc.setTextColor(0, 0, 0);
                doc.text('Status Distribution:', margin, yPosition);
                yPosition += 10;

                let xPos = margin;
                Object.entries(reportData.chartData.by_status).forEach(([status, count]) => {
                    doc.setFontSize(10);
                    doc.text(`${this.capitalizeFirst(status)}: ${count}`, xPos, yPosition);
                    xPos += 60;
                    if (xPos > pageWidth - 60) {
                        xPos = margin;
                        yPosition += 8;
                    }
                });
                yPosition += 20;
            }
        }

        // Data Table (optimized for speed)
        if (limitedData && limitedData.length > 0) {
            // Check if we need a new page
            if (yPosition > pageHeight - 100) {
                doc.addPage();
                yPosition = margin;
            }

            doc.setFontSize(14);
            doc.setTextColor(...primaryColor);
            const totalRecords = reportData.data ? reportData.data.length : limitedData.length;
            const showingRecords = Math.min(limitedData.length, maxRecords);
            doc.text(`Detailed Data (showing ${showingRecords} of ${totalRecords} records)`, margin, yPosition);
            yPosition += 15;

            // Table headers (optimized)
            const headers = ['ID', 'Student ID', 'Name', 'Type', 'Status', 'Course', 'GWA'];
            const colWidths = [15, 25, 40, 25, 20, 35, 15];
            let xPos = margin;

            doc.setFontSize(9);
            doc.setTextColor(255, 255, 255);
            doc.setFillColor(...primaryColor);

            // Draw header background (single operation)
            doc.rect(margin, yPosition - 5, pageWidth - 2 * margin, 10, 'F');

            // Draw headers (batch operation)
            headers.forEach((header, index) => {
                doc.text(header, xPos + 2, yPosition + 2);
                xPos += colWidths[index];
            });

            yPosition += 10;

            // Table rows (optimized rendering)
            doc.setTextColor(0, 0, 0);
            const maxRowsPerPage = Math.min(limitedData.length, 25); // Limit rows for faster rendering

            limitedData.slice(0, maxRowsPerPage).forEach((row, index) => {
                if (yPosition > pageHeight - 30) {
                    doc.addPage();
                    yPosition = margin;
                }

                xPos = margin;

                // Alternate row colors (optimized)
                if (index % 2 === 0) {
                    doc.setFillColor(...lightGray);
                    doc.rect(margin, yPosition - 3, pageWidth - 2 * margin, 8, 'F');
                }

                // Pre-process row data for faster rendering
                const rowData = [
                    row.id || 'N/A',
                    row.student_id || 'N/A',
                    this.truncateText(row.full_name || 'N/A', 25),
                    this.capitalizeFirst(row.scholarship_type || 'N/A'),
                    row.status || 'N/A',
                    this.truncateText(row.course || 'N/A', 20),
                    row.gwa || 'N/A'
                ];

                doc.setFontSize(8);
                rowData.forEach((data, colIndex) => {
                    doc.text(data.toString(), xPos + 2, yPosition + 2);
                    xPos += colWidths[colIndex];
                });

                yPosition += 8;
            });

            // Show remaining records info
            if (totalRecords > maxRowsPerPage) {
                yPosition += 5;
                doc.setFontSize(10);
                doc.setTextColor(...secondaryColor);
                doc.text(`... and ${totalRecords - maxRowsPerPage} more records (download full report for complete data)`, margin, yPosition);
            }
        }

        // Footer
        const totalPages = doc.internal.getNumberOfPages();
        for (let i = 1; i <= totalPages; i++) {
            doc.setPage(i);
            doc.setFontSize(8);
            doc.setTextColor(...secondaryColor);
            doc.text(
                `Page ${i} of ${totalPages} | Generated by Scholarship Management System`,
                pageWidth / 2,
                pageHeight - 10,
                { align: 'center' }
            );
        }

        // Save the PDF
        const filename = `${reportType.replace(/\s+/g, '_').toLowerCase()}_report_${new Date().toISOString().split('T')[0]}.pdf`;
        doc.save(filename);
    }

    /**
     * Helper methods
     */
    formatLabel(key) {
        return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    truncateText(text, maxLength) {
        return text.length > maxLength ? text.substring(0, maxLength - 3) + '...' : text;
    }
}

// Initialize the PDF generator
window.ReportPDFGenerator = ReportPDFGenerator;
