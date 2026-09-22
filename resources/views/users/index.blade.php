@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-[#0F2854]">Manajemen Pengguna</h1>
            <p class="text-gray-500 mt-1">Daftar pengguna terdaftar di sistem beserta hak aksesnya.</p>
        </div>
        <button onclick="openUserModal()" class="bg-[#1C4D8D] hover:bg-[#0F2854] text-white px-5 py-2.5 rounded-lg shadow-md font-medium transition flex items-center">
            <i class="fas fa-user-plus mr-2"></i> Tambah Pengguna
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-4 px-6 font-medium">Nama</th>
                        <th class="py-4 px-6 font-medium">Email / Kontak</th>
                        <th class="py-4 px-6 font-medium">Peran (Role)</th>
                        <th class="py-4 px-6 font-medium">Status</th>
                        <th class="py-4 px-6 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody" class="divide-y divide-gray-100 text-gray-700">
                    <tr><td colspan="5" class="py-8 text-center text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div id="userModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform scale-95 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        <div class="bg-[#0F2854] px-6 py-4 flex justify-between items-center text-white sticky top-0 z-10">
            <h3 class="text-lg font-bold" id="userModalTitle">Tambah Pengguna</h3>
            <button onclick="closeUserModal()" class="text-gray-300 hover:text-white transition"><i class="fas fa-times text-xl"></i></button>
        </div>
        <form id="userForm" onsubmit="saveUser(event)" class="p-6 space-y-4">
            <input type="hidden" id="userId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" id="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#1C4D8D] focus:border-[#1C4D8D]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#1C4D8D] focus:border-[#1C4D8D]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" placeholder="Kosongkan jika tidak ingin mengubah" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#1C4D8D] focus:border-[#1C4D8D]">
                <p class="text-xs text-gray-500 mt-1" id="passwordHelp">Minimal 8 karakter.</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select id="role" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#1C4D8D] focus:border-[#1C4D8D]">
                        <option value="peserta">Peserta</option>
                        <option value="penguji">Penguji</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="is_active" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#1C4D8D] focus:border-[#1C4D8D]">
                        <option value="1">Aktif</option>
                        <option value="0">Non-Aktif</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                    <input type="text" id="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#1C4D8D] focus:border-[#1C4D8D]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIP (Khusus Penguji/Admin)</label>
                    <input type="text" id="nip" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#1C4D8D] focus:border-[#1C4D8D]">
                </div>
            </div>
            
            <div class="pt-4 flex justify-end gap-3 sticky bottom-0 bg-white">
                <button type="button" onclick="closeUserModal()" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-[#1C4D8D] text-white font-medium rounded-lg hover:bg-[#0F2854] transition shadow">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
let usersList = [];
const api = axios.create({
    headers: { 
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    withCredentials: true
});

document.addEventListener('DOMContentLoaded', fetchUsers);

async function fetchUsers() {
    const tableBody = document.getElementById('usersTableBody');
    tableBody.innerHTML = '<tr><td colspan="5" class="py-8 text-center text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...</td></tr>';
    
    try {
        const response = await api.get('/api/users');
        usersList = response.data.data;
        renderUsersTable();
    } catch (error) {
        tableBody.innerHTML = '<tr><td colspan="5" class="py-8 text-center text-red-500">Gagal memuat data pengguna.</td></tr>';
    }
}

function renderUsersTable() {
    const tableBody = document.getElementById('usersTableBody');
    if (!usersList || usersList.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="5" class="py-8 text-center text-gray-500">Belum ada pengguna.</td></tr>';
        return;
    }

    tableBody.innerHTML = usersList.map(user => {
        let roleBadge = '';
        if(user.role === 'admin') roleBadge = '<span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold">Admin</span>';
        else if(user.role === 'penguji') roleBadge = '<span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">Penguji</span>';
        else roleBadge = '<span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">Peserta</span>';

        const statusBadge = user.is_active 
            ? '<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Aktif</span>'
            : '<span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Non-Aktif</span>';

        return `
            <tr class="hover:bg-blue-50 transition-colors">
                <td class="py-4 px-6">
                    <div class="font-bold text-gray-800">${user.name}</div>
                    ${user.nip ? `<div class="text-xs text-gray-500">NIP: ${user.nip}</div>` : ''}
                </td>
                <td class="py-4 px-6">
                    <div class="text-gray-800">${user.email}</div>
                    <div class="text-xs text-gray-500">${user.phone || '-'}</div>
                </td>
                <td class="py-4 px-6">${roleBadge}</td>
                <td class="py-4 px-6">${statusBadge}</td>
                <td class="py-4 px-6 text-right">
                    <button onclick="editUser(${user.id})" class="text-[#1C4D8D] hover:text-[#0F2854] bg-blue-50 hover:bg-blue-100 p-2 rounded transition mr-1" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 p-2 rounded transition" title="Hapus">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function openUserModal(user = null) {
    const modal = document.getElementById('userModal');
    const form = document.getElementById('userForm');
    const passwordInput = document.getElementById('password');
    const passwordHelp = document.getElementById('passwordHelp');
    
    if (user) {
        document.getElementById('userModalTitle').innerText = 'Edit Pengguna';
        document.getElementById('userId').value = user.id;
        document.getElementById('name').value = user.name;
        document.getElementById('email').value = user.email;
        document.getElementById('role').value = user.role;
        document.getElementById('is_active').value = user.is_active ? "1" : "0";
        document.getElementById('phone').value = user.phone || '';
        document.getElementById('nip').value = user.nip || '';
        
        passwordInput.required = false;
        passwordHelp.innerText = 'Kosongkan jika tidak ingin mengubah password.';
    } else {
        document.getElementById('userModalTitle').innerText = 'Tambah Pengguna';
        form.reset();
        document.getElementById('userId').value = '';
        
        passwordInput.required = true;
        passwordHelp.innerText = 'Minimal 8 karakter. (Wajib)';
    }

    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.querySelector('div').classList.remove('scale-95');
    }, 10);
}

function closeUserModal() {
    const modal = document.getElementById('userModal');
    modal.classList.add('opacity-0');
    modal.querySelector('div').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function editUser(id) {
    const user = usersList.find(u => u.id === id);
    if(user) openUserModal(user);
}

async function saveUser(e) {
    e.preventDefault();
    const id = document.getElementById('userId').value;
    const data = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        role: document.getElementById('role').value,
        is_active: document.getElementById('is_active').value,
        phone: document.getElementById('phone').value,
        nip: document.getElementById('nip').value
    };

    const password = document.getElementById('password').value;
    if(password) {
        data.password = password;
    }

    Notiflix.Block.standard('#userModal > div', 'Menyimpan...');
    try {
        if (id) {
            await api.put(`/api/users/${id}`, data);
            Notiflix.Notify.success('Pengguna berhasil diperbarui');
        } else {
            await api.post('/api/users', data);
            Notiflix.Notify.success('Pengguna berhasil ditambahkan');
        }
        closeUserModal();
        fetchUsers();
    } catch (error) {
        Notiflix.Notify.failure(error.response?.data?.message || 'Gagal menyimpan pengguna');
    } finally {
        Notiflix.Block.remove('#userModal > div');
    }
}

function deleteUser(id) {
    Notiflix.Confirm.show(
        'Konfirmasi Hapus',
        'Yakin ingin menghapus pengguna ini?',
        'Hapus', 'Batal',
        async function() {
            Notiflix.Block.standard('body', 'Menghapus...');
            try {
                await api.delete(`/api/users/${id}`);
                Notiflix.Notify.success('Pengguna berhasil dihapus');
                fetchUsers();
            } catch (error) {
                Notiflix.Notify.failure(error.response?.data?.message || 'Gagal menghapus pengguna');
            } finally {
                Notiflix.Block.remove('body');
            }
        }
    );
}
</script>
@endsection
