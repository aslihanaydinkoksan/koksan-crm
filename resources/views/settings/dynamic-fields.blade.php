@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-12 mb-3 d-flex justify-content-between align-items-center">
            <h4 class="text-secondary fw-bold"><i class="bi bi-sliders me-2"></i>Dinamik Form Yönetimi (EAV)</h4>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-dark">
                <div class="card-header bg-dark text-white fw-bold">
                    <h5 class="mb-0">Yeni Alan Ekle</h5>
                </div>
                <div class="card-body">
                    <form id="dynamicFieldForm">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hedef Modül</label>
                            <select id="model_type" name="model_type" class="form-select" required>
                                <option value="">Modül Seçiniz...</option>
                                @foreach ($supportedModels as $class => $label)
                                    <option value="{{ $class }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Görünen İsim (Label)</label>
                            <input type="text" id="label_input" name="label" class="form-control"
                                placeholder="Örn: Vergi Dairesi" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-success fw-bold">Sistem Anahtarı (JSON Key)</label>
                            <input type="text" id="name_input" name="name"
                                class="form-control text-success font-monospace bg-light" placeholder="otomatik_dolar"
                                pattern="^[a-z_]+$" title="Sadece küçük harf ve alt çizgi" required readonly>
                            <small class="text-muted">Görünen isme göre otomatik oluşturulur.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-primary">Veri Tipi</label>
                            <select id="field_type" name="type" class="form-select border-primary" required>
                                <option value="text">Kısa Metin (Text)</option>
                                <option value="number">Sayısal (Number)</option>
                                <option value="date">Tarih (Date)</option>
                                <option value="select">Açılır Kutu (Select)</option>
                            </select>
                        </div>

                        <div class="mb-3 d-none" id="options_wrapper">
                            <label class="form-label text-danger fw-bold">Seçenekler <small>(Virgülle
                                    Ayırın)</small></label>
                            <input type="text" id="options_input" name="options" class="form-control border-danger"
                                placeholder="Örn: KG, TON, ADET">
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_required" id="is_required"
                                value="1">
                            <label class="form-check-label fw-bold" for="is_required">Bu alan zorunlu mu?</label>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 fw-bold">Sisteme Ekle</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white d-flex justify-content-between">
                    <h5 class="mb-0">Mevcut Dinamik Alanlar</h5>
                    <select id="filter_model" class="form-select form-select-sm w-auto">
                        <option value="">Tüm Modüller</option>
                        @foreach ($supportedModels as $class => $label)
                            <option value="{{ $class }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Modül</th>
                                <th>Etiket (Label)</th>
                                <th>Anahtar (Key)</th>
                                <th>Tip</th>
                                <th>Zorunlu</th>
                                <th class="text-end">İşlem</th>
                            </tr>
                        </thead>
                        <tbody id="fieldsTableBody">
                            <tr>
                                <td colspan="6" class="text-center py-4">Kayıtlar yükleniyor...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editFieldModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Dinamik Alanı Düzenle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="editFieldForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit_id">

                        <div class="alert alert-warning py-2 mb-3">
                            <small><i class="bi bi-exclamation-triangle"></i> Sistem anahtarı (JSON Key) veri bütünlüğünü
                                korumak adına sonradan değiştirilemez.</small>
                        </div>
                        <div class="mb-2">
                            <label class="form-label text-muted">Sistem Anahtarı</label>
                            <input type="text" id="edit_name" class="form-control bg-light" readonly>
                        </div>

                        <div class="mb-3 mt-3">
                            <label class="form-label">Görünen İsim (Label)</label>
                            <input type="text" id="edit_label" name="label" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-primary">Veri Tipi</label>
                            <select id="edit_type" name="type" class="form-select border-primary" required>
                                <option value="text">Kısa Metin (Text)</option>
                                <option value="number">Sayısal (Number)</option>
                                <option value="date">Tarih (Date)</option>
                                <option value="select">Açılır Kutu (Select)</option>
                            </select>
                        </div>

                        <div class="mb-3 d-none" id="edit_options_wrapper">
                            <label class="form-label text-danger">Seçenekler <small>(Virgülle Ayırın)</small></label>
                            <input type="text" id="edit_options" name="options" class="form-control border-danger">
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_required" id="edit_is_required"
                                value="1">
                            <label class="form-check-label fw-bold" for="edit_is_required">Bu alan zorunlu mu?</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const API_HEADERS = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
        const modelLabels = @json($supportedModels);
        let currentFields = []; // Edit için hafızada tutuyoruz

        // Bootstrap Modal Instance
        let editModalInstance;

        document.addEventListener('DOMContentLoaded', () => {
            loadFields();
            editModalInstance = new bootstrap.Modal(document.getElementById('editFieldModal'));
        });

        // 1. OTOMATİK SLUG / JSON KEY ÜRETİCİ (Türkçe Karakter Desteği)
        document.getElementById('label_input').addEventListener('input', function(e) {
            const text = e.target.value;
            const trMap = {
                'çÇ': 'c',
                'ğĞ': 'g',
                'şŞ': 's',
                'üÜ': 'u',
                'ıİ': 'i',
                'öÖ': 'o'
            };
            let slug = text;
            for (let key in trMap) {
                slug = slug.replace(new RegExp('[' + key + ']', 'g'), trMap[key]);
            }
            slug = slug.toLowerCase()
                .replace(/[^a-z0-9]/g, '_') // Harf ve rakam dışındakileri alt çizgi yap
                .replace(/_+/g, '_') // Birden fazla alt çizgiyi teke düşür
                .replace(/^_|_$/g, ''); // Baş ve sondaki alt çizgileri temizle

            document.getElementById('name_input').value = slug;
        });

        // Yeni Kayıt için Options Göster/Gizle
        document.getElementById('field_type').addEventListener('change', function() {
            toggleOptionsWrapper('options_wrapper', 'options_input', this.value);
        });

        // Düzenleme (Edit) için Options Göster/Gizle
        document.getElementById('edit_type').addEventListener('change', function() {
            toggleOptionsWrapper('edit_options_wrapper', 'edit_options', this.value);
        });

        function toggleOptionsWrapper(wrapperId, inputId, value) {
            const wrapper = document.getElementById(wrapperId);
            const input = document.getElementById(inputId);
            if (value === 'select') {
                wrapper.classList.remove('d-none');
                input.setAttribute('required', 'required');
            } else {
                wrapper.classList.add('d-none');
                input.removeAttribute('required');
                input.value = '';
            }
        }

        document.getElementById('filter_model').addEventListener('change', () => loadFields());

        // Tabloyu Doldur
        async function loadFields() {
            const filterModel = document.getElementById('filter_model').value;
            const endpoint = filterModel ? `/api/dynamic-fields?model_type=${encodeURIComponent(filterModel)}` :
                '/api/dynamic-fields';

            const response = await fetch(endpoint, {
                headers: API_HEADERS
            });
            const result = await response.json();
            const tbody = document.getElementById('fieldsTableBody');
            tbody.innerHTML = '';

            if (result.data.length === 0) return tbody.innerHTML =
                '<tr><td colspan="6" class="text-center py-4">Dinamik alan bulunamadı.</td></tr>';

            currentFields = result.data; // Modal için hafızaya al

            result.data.forEach(f => {
                let reqBadge = f.is_required ? '<span class="badge bg-danger">Evet</span>' :
                    '<span class="badge bg-secondary">Hayır</span>';
                let modLabel = modelLabels[f.model_type] || f.model_type.split('\\').pop();
                let options = f.type === 'select' ?
                    `<br><small class="text-muted">[${f.options.join(', ')}]</small>` : '';

                tbody.innerHTML += `<tr>
                <td class="fw-bold text-secondary">${modLabel}</td>
                <td class="fw-bold">${f.label}</td>
                <td><code>${f.name}</code></td>
                <td><span class="badge border border-primary text-primary">${f.type.toUpperCase()}</span> ${options}</td>
                <td>${reqBadge}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="openEditModal(${f.id})"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteField(${f.id})"><i class="bi bi-trash"></i></button>
                </td>
            </tr>`;
            });
        }

        // Modal'ı Aç ve Verileri Doldur
        function openEditModal(id) {
            const field = currentFields.find(f => f.id === id);
            if (!field) return;

            document.getElementById('edit_id').value = field.id;
            document.getElementById('edit_name').value = field.name;
            document.getElementById('edit_label').value = field.label;
            document.getElementById('edit_type').value = field.type;
            document.getElementById('edit_is_required').checked = field.is_required;

            const optionsInput = document.getElementById('edit_options');
            if (field.type === 'select') {
                document.getElementById('edit_options_wrapper').classList.remove('d-none');
                optionsInput.value = field.options ? field.options.join(', ') : '';
                optionsInput.setAttribute('required', 'required');
            } else {
                document.getElementById('edit_options_wrapper').classList.add('d-none');
                optionsInput.value = '';
                optionsInput.removeAttribute('required');
            }

            editModalInstance.show();
        }

        // Yeni Ekleme
        document.getElementById('dynamicFieldForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {};
            new FormData(e.target).forEach((val, key) => payload[key] = val);
            if (!payload.is_required) payload.is_required = 0;

            const response = await fetch('/api/dynamic-fields', {
                method: 'POST',
                headers: API_HEADERS,
                body: JSON.stringify(payload)
            });
            const result = await response.json();

            if (response.ok) {
                alert('Alan başarıyla eklendi!');
                e.target.reset();
                document.getElementById('options_wrapper').classList.add('d-none');
                loadFields();
            } else {
                alert(result.message || 'Kayıt başarısız! Veritabanı anahtarı çakışıyor olabilir.');
            }
        });

        // Güncelleme
        document.getElementById('editFieldForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('edit_id').value;
            const payload = {};
            new FormData(e.target).forEach((val, key) => payload[key] = val);
            if (!payload.is_required) payload.is_required = 0;

            const response = await fetch(`/api/dynamic-fields/${id}`, {
                method: 'PUT',
                headers: API_HEADERS,
                body: JSON.stringify(payload)
            });

            if (response.ok) {
                editModalInstance.hide();
                loadFields();
            } else {
                alert('Güncelleme başarısız oldu!');
            }
        });

        // Silme
        async function deleteField(id) {
            if (!confirm('Bu alanı silmek istediğinize emin misiniz?')) return;
            const response = await fetch(`/api/dynamic-fields/${id}`, {
                method: 'DELETE',
                headers: API_HEADERS
            });
            if (response.ok) loadFields();
        }
    </script>
@endsection
