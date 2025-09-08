{{-- resources/views/products/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Lista de Produtos')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Sistema de Estoque</h1>
                <div>
                    <span class="badge bg-secondary me-2">{{ $products->count() }} produtos</span>
                    <button type="button" class="btn btn-primary" onclick="syncWithApi()">
                        <i class="fas fa-sync-alt"></i> Sincronizar com API
                    </button>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt"></i> Sair
                        </button>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Produtos Cadastrados</h5>
                </div>
                <div class="card-body p-0">
                    @if($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60">Imagem</th>
                                        <th>Produto</th>
                                        <th>Categoria</th>
                                        <th>Preço</th>
                                        <th width="120">Estoque</th>
                                        <th width="100">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                @if($product->imagem)
                                                    <img src="{{ $product->image }}" 
                                                         alt="{{ $product->name }}" 
                                                         class="img-thumbnail" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $product->nome }}</strong>
                                                @if($product->description)
                                                    <br><small class="text-muted">{{ Str::limit($product->description, 60) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->category)
                                                    <span class="badge bg-info">{{ ucfirst($product->category) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="text-success">{{ $product->price }}</strong>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" 
                                                           class="form-control" 
                                                           value="{{ $product->stock }}" 
                                                           id="estoque_{{ $product->id }}"
                                                           min="0">
                                                </div>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-success" 
                                                        onclick="updateStock({{ $product->id }})">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5>Nenhum produto cadastrado</h5>
                            <p class="text-muted">Clique em "Sincronizar com API" para importar produtos</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status"></div>
                <p class="mb-0">Sincronizando produtos...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Atualizar estoque
function updateStock(productId) {
    const estoque = document.getElementById(`estoque_${productId}`).value;
    
    fetch(`/produtos/${productId}/estoque`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ stock: parseInt(estoque) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar toast de sucesso
            showToast(data.message, 'success');
        } else {
            showToast('Erro ao atualizar estoque', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro ao atualizar estoque', 'error');
    });
}

// Sincronizar com API
function syncWithApi() {
    const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
    modal.show();
    
    fetch('/produtos/sync-api', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        modal.hide();
        window.location.reload();
    })
    .catch(error => {
        console.error('Erro:', error);
        modal.hide();
        showToast('Erro ao sincronizar produtos', 'error');
    });
}

// Mostrar toast
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}
</script>
@endsection