/**
 * Mengonversi array of objects menjadi file CSV dan mengunduhnya.
 * @param {Array} data - Array of objects yang akan diekspor.
 * @param {String} filename - Nama file (tanpa ekstensi .csv).
 * @param {Object} columnMapping - Mapping kolom, misal: { "id": "ID", "title": "Judul" }
 */
export function exportToCSV(data, filename, columnMapping) {
  if (!data || !data.length) {
    alert("Tidak ada data untuk diekspor.");
    return;
  }

  // Dapatkan semua keys dari object pertama jika columnMapping tidak disediakan
  const keys = columnMapping ? Object.keys(columnMapping) : Object.keys(data[0]);
  const headers = columnMapping ? Object.values(columnMapping) : keys;

  let csvContent = headers.join(",") + "\n";

  data.forEach((row) => {
    const rowValues = keys.map(key => {
      let cellValue = row[key] === null || row[key] === undefined ? "" : row[key].toString();
      // Handle commas or quotes in data
      cellValue = cellValue.replace(/"/g, '""');
      if (cellValue.includes(",") || cellValue.includes("\"") || cellValue.includes("\n")) {
        cellValue = `"${cellValue}"`;
      }
      return cellValue;
    });
    csvContent += rowValues.join(",") + "\n";
  });

  // Blob untuk CSV
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  
  // Buat link temporary untuk trigger download
  const link = document.createElement("a");
  link.setAttribute("href", url);
  link.setAttribute("download", `${filename}_${new Date().toISOString().slice(0,10)}.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}
