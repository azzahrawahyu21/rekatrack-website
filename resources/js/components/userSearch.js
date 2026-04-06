export default function searchUserComponent() {
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
        const res = await fetch(`/user-search?search=${encodeURIComponent(this.query)}`, {
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
      const tbody = document.getElementById('user-table-body');
      tbody.innerHTML = '';

      if (results.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="7" class="text-center py-4 text-gray-500">Tidak ada data ditemukan</td>
          </tr>`;
        return;
      }

      results.forEach((user, index) => {
        tbody.innerHTML += `
          <tr>
            <td class="px-5 py-4 sm:px-6">
              <p class="text-gray-500 text-theme-sm dark:text-gray-400">${index + 1}</p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p class="text-gray-500 text-theme-sm dark:text-gray-400">${user.nip || '-'}</p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p class="text-gray-500 text-theme-sm dark:text-gray-400">${user.name}</p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p class="text-gray-500 text-theme-sm dark:text-gray-400">${user.email}</p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p class="text-gray-500 text-theme-sm dark:text-gray-400">${user.phone_number || '-'}</p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p class="text-gray-500 text-theme-sm dark:text-gray-400">${user.role?.name || '-'}</p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <div class="flex space-x-2">
                <form action="/users/${user.id}/edit" method="GET">
                  <button type="submit" class="text-blue-600 dark:text-blue-400 hover:underline">Edit</button>
                </form>
                <form action="/users/${user.id}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus?');">
                  <input type="hidden" name="_method" value="DELETE" />
                  <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}" />
                  <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        `;
      });
    }
  }
}
