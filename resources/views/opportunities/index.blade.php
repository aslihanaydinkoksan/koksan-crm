@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-12 mb-3 d-flex justify-content-between align-items-center">
            <h4 class="text-secondary fw-bold">Satış Fırsatları & Duyumlar</h4>
        </div>

        <div class="col-12">
            <div class="card shadow-sm border-success">
                <div class="card-header bg-success text-white d-flex justify-content-between">
                    <h5 class="mb-0">Fırsat Listesi</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Başlık / Konu</th>
                                <th>Müşteri</th>
                                <th>Tahmini Tutar</th>
                                <th>Aşama</th>
                            </tr>
                        </thead>
                        <tbody id="opportunitiesTableBody">
                            <tr>
                                <td colspan="4" class="text-center">Fırsatlar yükleniyor...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const response = await fetch('/api/opportunities', {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
            const tbody = document.getElementById('opportunitiesTableBody');
            tbody.innerHTML = '';

            if (result.data.length === 0) {
                return tbody.innerHTML =
                    '<tr><td colspan="4" class="text-center">Kayıtlı fırsat bulunamadı.</td></tr>';
            }

            result.data.forEach(opp => {
                // Aşamaya göre renk belirleme
                let badgeColor = 'secondary';
                if (opp.stage === 'kazanildi') badgeColor = 'success';
                if (opp.stage === 'kaybedildi') badgeColor = 'danger';
                if (opp.stage === 'teklif') badgeColor = 'primary';

                tbody.innerHTML += `
                <tr>
                    <td class="fw-bold">${opp.title}</td>
                    <td>${opp.customer ? opp.customer.company_name : '-'}</td>
                    <td class="text-success fw-bold">${opp.formatted_amount}</td>
                    <td><span class="badge bg-${badgeColor}">${opp.stage.toUpperCase()}</span></td>
                </tr>
            `;
            });
        });
    </script>
@endsection
