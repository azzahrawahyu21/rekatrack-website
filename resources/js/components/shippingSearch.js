export default function searchDocumentComponent() {
  return {
    query: '',
    loading: false,
    errorMessage: '',

    async search() {
      if (this.query.trim() === '') {
        window.location.reload();
        return;
      }

      this.loading = true;
      this.errorMessage = '';

      try {
        const res = await fetch(`/shippings-search?search=${encodeURIComponent(this.query)}`, {
          headers: { 'Accept': 'application/json' },
        });

        if (!res.ok) throw new Error('Gagal mengambil data');

        const data = await res.json();
        this.updateTable(data.results);
      } catch (error) {
        this.errorMessage = error.message || 'Terjadi kesalahan';
      } finally {
        this.loading = false;
      }
    },

    updateTable(results) {
      const tbody = document.getElementById('shipping-table-body');
      tbody.innerHTML = '';

      if (results.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="8" class="text-center py-4 text-gray-500">Tidak ada data ditemukan</td>
          </tr>`;
        return;
      }

      results.forEach((item, index) => {
        const dateObj = new Date(item.date_no_travel_document);
        const formattedDate = ("0" + dateObj.getDate()).slice(-2) + '/' +
                              ("0" + (dateObj.getMonth() + 1)).slice(-2) + '/' +
                              dateObj.getFullYear();

        tbody.innerHTML += `
          <tr>
            <td class="px-2 py-4 sm:px-6">
              <p class="text-gray-500 text-theme-sm dark:text-gray-400">${index + 1}</p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p class="text-gray-500 text-theme-sm dark:text-gray-400">${item.no_travel_document}</p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p class="text-gray-500 text-theme-sm dark:text-gray-400">${formattedDate}</p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p class="text-gray-500 text-theme-sm dark:text-gray-400">${item.send_to}</p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p class="text-gray-500 text-theme-sm dark:text-gray-400">${item.project || ''}</p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p class="text-gray-500 text-theme-sm dark:text-gray-400">${item.status || ''}</p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <div class="flex space-x-2">
                <form action="/shippings/${item.id}" method="GET">
                  <button type="submit" class="text-blue-600 dark:text-blue-400 hover:underline">detail</button>
                </form>
                <form action="/shippings/${item.id}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus?');">
                  <input type="hidden" name="_method" value="DELETE" />
                  <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}" />
                  <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Hapus</button>
                </form>
              </div>
            </td>
          </tr>`;
      });
    }
  }
}
