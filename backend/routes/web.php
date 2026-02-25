<?php

use App\Models\ClienteFornecedor;
use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }

    return redirect('/login');
});

Route::get('/dashboard', function () {
    if (! Auth::check()) {
        return redirect('/login');
    }

    return view('dashboard', [
        'user' => Auth::user(),
    ]);
});

Route::get('/login', function () {
    return view('login');
});

Route::post('/login', function (Request $request) {
    if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
        $request->session()->regenerate();

        return redirect('/dashboard');
    }

    return back()->with('error', 'Usuario ou senha invalido');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login');
});

Route::prefix('api')->group(function () {
    $ensureClientesFornecedoresTable = function (): void {
        if (! Schema::hasTable('clientes_fornecedores')) {
            Schema::create('clientes_fornecedores', function (Blueprint $table) {
                $table->id();
                $table->string('tipo', 30)->default('cliente_fornecedor');
                $table->string('nome_razao_social');
                $table->string('nome_fantasia', 255)->nullable();
                $table->string('cnpj_cpf', 20)->nullable();
                $table->string('ie_rg', 20)->nullable();
                $table->string('telefone_contato', 20)->nullable();
                $table->string('email_contato', 255)->nullable();
                $table->string('cep', 9)->nullable();
                $table->string('endereco')->nullable();
                $table->string('numero', 30)->nullable();
                $table->string('complemento', 100)->nullable();
                $table->string('bairro', 100)->nullable();
                $table->string('cidade', 100)->nullable();
                $table->string('uf', 2)->nullable();
                $table->timestamps();
            });

            return;
        }

        if (! Schema::hasColumn('clientes_fornecedores', 'nome_fantasia')) {
            Schema::table('clientes_fornecedores', function (Blueprint $table) {
                $table->string('nome_fantasia', 255)->nullable()->after('nome_razao_social');
            });
        }

        if (! Schema::hasColumn('clientes_fornecedores', 'telefone_contato')) {
            Schema::table('clientes_fornecedores', function (Blueprint $table) {
                $table->string('telefone_contato', 20)->nullable()->after('ie_rg');
            });
        }

        if (! Schema::hasColumn('clientes_fornecedores', 'email_contato')) {
            Schema::table('clientes_fornecedores', function (Blueprint $table) {
                $table->string('email_contato', 255)->nullable()->after('telefone_contato');
            });
        }
    };

    $ensureItensTable = function (): void {
        if (Schema::hasTable('itens')) {
            return;
        }

        Schema::create('itens', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('descricao');
            $table->string('unidade_medida', 20)->nullable();
            $table->decimal('preco', 12, 2)->default(0);
            $table->integer('estoque')->default(0);
            $table->timestamps();
        });
    };

    $ensureUnidadesMedidaTable = function (): void {
        if (Schema::hasTable('unidades_medida')) {
            return;
        }

        Schema::create('unidades_medida', function (Blueprint $table) {
            $table->id();
            $table->string('sigla', 20)->unique();
            $table->string('descricao', 100)->nullable();
            $table->timestamps();
        });

        DB::table('unidades_medida')->insert([
            ['sigla' => 'UN', 'descricao' => 'Unidade', 'created_at' => now(), 'updated_at' => now()],
            ['sigla' => 'KG', 'descricao' => 'Quilograma', 'created_at' => now(), 'updated_at' => now()],
            ['sigla' => 'CX', 'descricao' => 'Caixa', 'created_at' => now(), 'updated_at' => now()],
            ['sigla' => 'LT', 'descricao' => 'Litro', 'created_at' => now(), 'updated_at' => now()],
            ['sigla' => 'M', 'descricao' => 'Metro', 'created_at' => now(), 'updated_at' => now()],
        ]);
    };

    $ensureCotacoesTables = function () use ($ensureClientesFornecedoresTable, $ensureItensTable): void {
        $ensureClientesFornecedoresTable();
        $ensureItensTable();

        if (! Schema::hasTable('cotacoes')) {
            Schema::create('cotacoes', function (Blueprint $table) {
                $table->id();
                $table->string('numero', 20)->unique();
                $table->unsignedBigInteger('cliente_fornecedor_id');
                $table->date('data_emissao');
                $table->decimal('total_geral', 14, 2)->default(0);
                $table->text('observacoes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('cotacao_itens')) {
            Schema::create('cotacao_itens', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cotacao_id');
                $table->unsignedBigInteger('item_id');
                $table->string('item_codigo', 20);
                $table->string('descricao');
                $table->string('unidade_medida', 20)->nullable();
                $table->decimal('preco_unitario', 12, 2);
                $table->integer('quantidade');
                $table->decimal('total_item', 14, 2);
                $table->timestamps();
            });
        }
    };

    $itemEstaEmUso = function (int $itemId, string $codigo): bool {
        $codigoLimpo = trim($codigo);

        if (! Schema::hasTable('cotacao_itens')) {
            return false;
        }

        if (Schema::hasColumn('cotacao_itens', 'item_id')) {
            $emUsoPorId = DB::table('cotacao_itens')
                ->where('item_id', $itemId)
                ->exists();

            if ($emUsoPorId) {
                return true;
            }
        }

        if ($codigoLimpo !== '') {
            $colunasCodigo = ['item_codigo', 'codigo_item', 'codigo'];

            foreach ($colunasCodigo as $colunaCodigo) {
                if (! Schema::hasColumn('cotacao_itens', $colunaCodigo)) {
                    continue;
                }

                $emUsoPorCodigo = DB::table('cotacao_itens')
                    ->where($colunaCodigo, $codigoLimpo)
                    ->exists();

                if ($emUsoPorCodigo) {
                    return true;
                }
            }
        }

        return false;
    };

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], (bool) ($credentials['remember'] ?? false))) {
            return response()->json([
                'message' => 'Credenciais invalidas.',
            ], 422);
        }

        $request->session()->regenerate();

        return response()->json([
            'message' => 'Login realizado com sucesso.',
            'user' => Auth::user(),
        ]);
    });

    Route::get('/me', function () {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        return response()->json([
            'user' => Auth::user(),
        ]);
    });

    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ]);
    });

    Route::get('/clientes-fornecedores', function () use ($ensureClientesFornecedoresTable) {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        $ensureClientesFornecedoresTable();

        $items = ClienteFornecedor::query()
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'items' => $items,
        ]);
    });

    Route::post('/clientes-fornecedores', function (Request $request) use ($ensureClientesFornecedoresTable) {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        $ensureClientesFornecedoresTable();

        $cep = preg_replace('/\D+/', '', (string) $request->input('cep', ''));
        $uf = strtoupper(trim((string) $request->input('uf', '')));

        $request->merge([
            'cep' => $cep !== '' ? $cep : null,
            'uf' => $uf !== '' ? $uf : null,
        ]);

        $data = $request->validate([
            'tipo' => ['nullable', 'string', 'max:30'],
            'nome_razao_social' => ['required', 'string', 'max:255'],
            'nome_fantasia' => ['nullable', 'string', 'max:255'],
            'cnpj_cpf' => ['nullable', 'string', 'max:20'],
            'ie_rg' => ['nullable', 'string', 'max:20'],
            'telefone_contato' => ['nullable', 'string', 'max:20'],
            'email_contato' => ['nullable', 'email', 'max:255'],
            'cep' => ['nullable', 'string', 'size:8'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:30'],
            'complemento' => ['nullable', 'string', 'max:100'],
            'bairro' => ['nullable', 'string', 'max:100'],
            'cidade' => ['nullable', 'string', 'max:100'],
            'uf' => ['nullable', 'string', 'size:2'],
        ]);

        $nomeNormalizado = preg_replace('/\s+/', ' ', trim((string) $data['nome_razao_social']));
        $nomeFantasiaNormalizado = preg_replace('/\s+/', ' ', trim((string) ($data['nome_fantasia'] ?? '')));
        $cnpjCpfNormalizado = preg_replace('/\D+/', '', (string) ($data['cnpj_cpf'] ?? ''));
        $telefoneContatoNormalizado = preg_replace('/\D+/', '', (string) ($data['telefone_contato'] ?? ''));
        $emailContatoNormalizado = Str::lower(trim((string) ($data['email_contato'] ?? '')));

        $data['nome_razao_social'] = $nomeNormalizado;
        $data['nome_fantasia'] = $nomeFantasiaNormalizado !== '' ? $nomeFantasiaNormalizado : null;
        $data['cnpj_cpf'] = $cnpjCpfNormalizado !== '' ? $cnpjCpfNormalizado : null;
        $data['telefone_contato'] = $telefoneContatoNormalizado !== '' ? $telefoneContatoNormalizado : null;
        $data['email_contato'] = $emailContatoNormalizado !== '' ? $emailContatoNormalizado : null;

        if ($data['cnpj_cpf']) {
            $cnpjDuplicado = ClienteFornecedor::query()
                ->where('cnpj_cpf', $data['cnpj_cpf'])
                ->exists();

            if ($cnpjDuplicado) {
                return response()->json([
                    'message' => 'Ja existe cadastro com este CNPJ/CPF.',
                ], 422);
            }
        }

        $nomeDuplicado = ClienteFornecedor::query()
            ->whereRaw('LOWER(nome_razao_social) = ?', [Str::lower($data['nome_razao_social'])])
            ->exists();

        if ($nomeDuplicado) {
            return response()->json([
                'message' => 'Ja existe cadastro com este Nome/Razao Social.',
            ], 422);
        }

        $item = ClienteFornecedor::create([
            ...$data,
            'tipo' => $data['tipo'] ?? 'cliente_fornecedor',
        ]);

        return response()->json([
            'message' => 'Cadastro salvo com sucesso.',
            'item' => $item,
        ], 201);
    });

    Route::put('/clientes-fornecedores/{id}', function (Request $request, int $id) use ($ensureClientesFornecedoresTable) {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        $ensureClientesFornecedoresTable();

        $item = ClienteFornecedor::query()->find($id);

        if (! $item) {
            return response()->json([
                'message' => 'Cadastro nao encontrado.',
            ], 404);
        }

        $cep = preg_replace('/\D+/', '', (string) $request->input('cep', ''));
        $uf = strtoupper(trim((string) $request->input('uf', '')));

        $request->merge([
            'cep' => $cep !== '' ? $cep : null,
            'uf' => $uf !== '' ? $uf : null,
        ]);

        $data = $request->validate([
            'tipo' => ['nullable', 'string', 'max:30'],
            'nome_razao_social' => ['required', 'string', 'max:255'],
            'nome_fantasia' => ['nullable', 'string', 'max:255'],
            'cnpj_cpf' => ['nullable', 'string', 'max:20'],
            'ie_rg' => ['nullable', 'string', 'max:20'],
            'telefone_contato' => ['nullable', 'string', 'max:20'],
            'email_contato' => ['nullable', 'email', 'max:255'],
            'cep' => ['nullable', 'string', 'size:8'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:30'],
            'complemento' => ['nullable', 'string', 'max:100'],
            'bairro' => ['nullable', 'string', 'max:100'],
            'cidade' => ['nullable', 'string', 'max:100'],
            'uf' => ['nullable', 'string', 'size:2'],
        ]);

        $nomeNormalizado = preg_replace('/\s+/', ' ', trim((string) $data['nome_razao_social']));
        $nomeFantasiaNormalizado = preg_replace('/\s+/', ' ', trim((string) ($data['nome_fantasia'] ?? '')));
        $cnpjCpfNormalizado = preg_replace('/\D+/', '', (string) ($data['cnpj_cpf'] ?? ''));
        $telefoneContatoNormalizado = preg_replace('/\D+/', '', (string) ($data['telefone_contato'] ?? ''));
        $emailContatoNormalizado = Str::lower(trim((string) ($data['email_contato'] ?? '')));

        $data['nome_razao_social'] = $nomeNormalizado;
        $data['nome_fantasia'] = $nomeFantasiaNormalizado !== '' ? $nomeFantasiaNormalizado : null;
        $data['cnpj_cpf'] = $cnpjCpfNormalizado !== '' ? $cnpjCpfNormalizado : null;
        $data['telefone_contato'] = $telefoneContatoNormalizado !== '' ? $telefoneContatoNormalizado : null;
        $data['email_contato'] = $emailContatoNormalizado !== '' ? $emailContatoNormalizado : null;

        if ($data['cnpj_cpf']) {
            $cnpjDuplicado = ClienteFornecedor::query()
                ->where('id', '!=', $item->id)
                ->where('cnpj_cpf', $data['cnpj_cpf'])
                ->exists();

            if ($cnpjDuplicado) {
                return response()->json([
                    'message' => 'Ja existe cadastro com este CNPJ/CPF.',
                ], 422);
            }
        }

        $nomeDuplicado = ClienteFornecedor::query()
            ->where('id', '!=', $item->id)
            ->whereRaw('LOWER(nome_razao_social) = ?', [Str::lower($data['nome_razao_social'])])
            ->exists();

        if ($nomeDuplicado) {
            return response()->json([
                'message' => 'Ja existe cadastro com este Nome/Razao Social.',
            ], 422);
        }

        $item->update([
            ...$data,
            'tipo' => $data['tipo'] ?? $item->tipo,
        ]);

        return response()->json([
            'message' => 'Cadastro atualizado com sucesso.',
            'item' => $item->fresh(),
        ]);
    });

    Route::delete('/clientes-fornecedores/{id}', function (int $id) use ($ensureClientesFornecedoresTable) {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        $ensureClientesFornecedoresTable();

        $item = ClienteFornecedor::query()->find($id);

        if (! $item) {
            return response()->json([
                'message' => 'Cadastro nao encontrado.',
            ], 404);
        }

        $item->delete();

        return response()->json([
            'message' => 'Cadastro excluido com sucesso.',
        ]);
    });

    Route::get('/cotacoes/contexto', function () use ($ensureCotacoesTables) {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        $ensureCotacoesTables();

        $clientes = DB::table('clientes_fornecedores')
            ->select('id', 'nome_razao_social', 'nome_fantasia')
            ->orderBy('nome_razao_social')
            ->get();

        $itens = DB::table('itens')
            ->select('id', 'codigo', 'descricao', 'unidade_medida', 'preco')
            ->orderBy('descricao')
            ->get();

        return response()->json([
            'clientes' => $clientes,
            'itens' => $itens,
        ]);
    });

    Route::get('/cotacoes/next-numero', function () use ($ensureCotacoesTables) {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        $ensureCotacoesTables();

        $ultimoNumero = (string) (DB::table('cotacoes')->max('numero') ?? '');
        $numero = (int) preg_replace('/\D+/', '', $ultimoNumero);
        $next = str_pad((string) ($numero + 1), 6, '0', STR_PAD_LEFT);

        return response()->json([
            'numero' => $next,
        ]);
    });

    Route::get('/cotacoes', function () use ($ensureCotacoesTables) {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        $ensureCotacoesTables();

        $items = DB::table('cotacoes as c')
            ->leftJoin('clientes_fornecedores as cf', 'cf.id', '=', 'c.cliente_fornecedor_id')
            ->leftJoin('cotacao_itens as ci', 'ci.cotacao_id', '=', 'c.id')
            ->select(
                'c.id',
                'c.numero',
                'c.data_emissao',
                'c.total_geral',
                'cf.nome_razao_social as cliente_nome'
            )
            ->selectRaw('COUNT(ci.id) as itens_count')
            ->groupBy('c.id', 'c.numero', 'c.data_emissao', 'c.total_geral', 'cf.nome_razao_social')
            ->orderByDesc('c.id')
            ->get();

        return response()->json([
            'items' => $items,
        ]);
    });

    Route::get('/cotacoes/{id}', function (int $id) use ($ensureCotacoesTables) {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        $ensureCotacoesTables();

        $cotacao = DB::table('cotacoes as c')
            ->leftJoin('clientes_fornecedores as cf', 'cf.id', '=', 'c.cliente_fornecedor_id')
            ->select(
                'c.id',
                'c.numero',
                'c.data_emissao',
                'c.total_geral',
                'c.observacoes',
                'cf.nome_razao_social as cliente_nome'
            )
            ->where('c.id', $id)
            ->first();

        if (! $cotacao) {
            return response()->json([
                'message' => 'Cotacao nao encontrada.',
            ], 404);
        }

        $itens = DB::table('cotacao_itens')
            ->select(
                'id',
                'item_codigo',
                'descricao',
                'unidade_medida',
                'preco_unitario',
                'quantidade',
                'total_item'
            )
            ->where('cotacao_id', $id)
            ->orderBy('id')
            ->get();

        return response()->json([
            'cotacao' => $cotacao,
            'itens' => $itens,
        ]);
    });

    Route::delete('/cotacoes/{id}', function (int $id) use ($ensureCotacoesTables) {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        $ensureCotacoesTables();

        $cotacao = DB::table('cotacoes')->where('id', $id)->first();

        if (! $cotacao) {
            return response()->json([
                'message' => 'Cotacao nao encontrada.',
            ], 404);
        }

        DB::transaction(function () use ($id) {
            DB::table('cotacao_itens')
                ->where('cotacao_id', $id)
                ->delete();

            DB::table('cotacoes')
                ->where('id', $id)
                ->delete();
        });

        return response()->json([
            'message' => 'Cotacao excluida com sucesso.',
        ]);
    });

    Route::post('/cotacoes', function (Request $request) use ($ensureCotacoesTables) {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        $ensureCotacoesTables();

        $data = $request->validate([
            'numero' => ['required', 'string', 'max:20', 'unique:cotacoes,numero'],
            'cliente_fornecedor_id' => ['required', 'integer', 'exists:clientes_fornecedores,id'],
            'data_emissao' => ['required', 'date'],
            'observacoes' => ['nullable', 'string'],
            'itens' => ['required', 'array', 'min:1'],
            'itens.*.item_id' => ['required', 'integer', 'exists:itens,id'],
            'itens.*.quantidade' => ['required', 'integer', 'min:1'],
        ]);

        $itensPayload = collect($data['itens']);
        $itemIds = $itensPayload->pluck('item_id')->map(fn ($id) => (int) $id)->unique()->values();
        $itensBase = DB::table('itens')
            ->whereIn('id', $itemIds)
            ->get()
            ->keyBy('id');

        $itensCotacao = [];
        $totalGeral = 0;

        foreach ($itensPayload as $itemInput) {
            $itemId = (int) $itemInput['item_id'];
            $quantidade = (int) $itemInput['quantidade'];
            $itemBase = $itensBase->get($itemId);

            if (! $itemBase) {
                return response()->json([
                    'message' => 'Item informado nao foi encontrado.',
                ], 422);
            }

            $preco = (float) $itemBase->preco;
            $totalItem = round($preco * $quantidade, 2);
            $totalGeral += $totalItem;

            $itensCotacao[] = [
                'item_id' => $itemId,
                'item_codigo' => $itemBase->codigo,
                'descricao' => $itemBase->descricao,
                'unidade_medida' => $itemBase->unidade_medida,
                'preco_unitario' => $preco,
                'quantidade' => $quantidade,
                'total_item' => $totalItem,
            ];
        }

        DB::transaction(function () use ($data, $itensCotacao, $totalGeral) {
            $cotacaoId = DB::table('cotacoes')->insertGetId([
                'numero' => trim((string) $data['numero']),
                'cliente_fornecedor_id' => (int) $data['cliente_fornecedor_id'],
                'data_emissao' => $data['data_emissao'],
                'total_geral' => $totalGeral,
                'observacoes' => trim((string) ($data['observacoes'] ?? '')) ?: null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $rows = array_map(function (array $item) use ($cotacaoId) {
                return [
                    'cotacao_id' => $cotacaoId,
                    'item_id' => $item['item_id'],
                    'item_codigo' => $item['item_codigo'],
                    'descricao' => $item['descricao'],
                    'unidade_medida' => $item['unidade_medida'],
                    'preco_unitario' => $item['preco_unitario'],
                    'quantidade' => $item['quantidade'],
                    'total_item' => $item['total_item'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $itensCotacao);

            DB::table('cotacao_itens')->insert($rows);
        });

        return response()->json([
            'message' => 'Cotacao salva com sucesso.',
        ], 201);
    });

    Route::get('/itens', function () use ($ensureItensTable, $itemEstaEmUso) {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        $ensureItensTable();

        $items = DB::table('itens')
            ->orderByDesc('id')
            ->get();

        $items = $items->map(function ($item) use ($itemEstaEmUso) {
            $item->em_uso = $itemEstaEmUso((int) $item->id, (string) $item->codigo);
            return $item;
        });

        return response()->json([
            'items' => $items,
        ]);
    });

    Route::get('/itens/next-codigo', function () use ($ensureItensTable) {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        $ensureItensTable();

        $lastCodigo = (string) (DB::table('itens')->max('codigo') ?? '');
        $numero = (int) preg_replace('/\D+/', '', $lastCodigo);
        $next = str_pad((string) ($numero + 1), 6, '0', STR_PAD_LEFT);

        return response()->json([
            'codigo' => $next,
        ]);
    });

    Route::post('/itens', function (Request $request) use ($ensureItensTable, $ensureUnidadesMedidaTable) {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        $ensureItensTable();
        $ensureUnidadesMedidaTable();

        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:20', 'unique:itens,codigo'],
            'descricao' => ['required', 'string', 'max:255'],
            'unidade_medida' => ['required', 'string', 'max:20', 'exists:unidades_medida,sigla'],
            'preco' => ['required', 'numeric', 'min:0'],
            'estoque' => ['required', 'integer', 'min:0'],
        ]);

        $data['descricao'] = preg_replace('/\s+/', ' ', trim((string) $data['descricao']));
        $data['unidade_medida'] = strtoupper(trim((string) $data['unidade_medida']));

        $itemDuplicado = DB::table('itens')
            ->whereRaw('LOWER(descricao) = ?', [Str::lower($data['descricao'])])
            ->where('unidade_medida', $data['unidade_medida'])
            ->exists();

        if ($itemDuplicado) {
            return response()->json([
                'message' => 'Ja existe item com esta descricao e unidade de medida.',
            ], 422);
        }

        DB::table('itens')->insert([
            'codigo' => $data['codigo'],
            'descricao' => $data['descricao'],
            'unidade_medida' => $data['unidade_medida'],
            'preco' => $data['preco'],
            'estoque' => $data['estoque'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Item salvo com sucesso.',
        ], 201);
    });

    Route::put('/itens/{id}', function (Request $request, int $id) use ($ensureItensTable, $ensureUnidadesMedidaTable, $itemEstaEmUso) {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        $ensureItensTable();
        $ensureUnidadesMedidaTable();

        $item = DB::table('itens')->where('id', $id)->first();

        if (! $item) {
            return response()->json([
                'message' => 'Item nao encontrado.',
            ], 404);
        }

        if ($itemEstaEmUso((int) $item->id, (string) $item->codigo)) {
            return response()->json([
                'message' => 'Nao e permitido editar item ja utilizado em outros registros.',
            ], 422);
        }

        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:20', 'unique:itens,codigo,' . $id],
            'descricao' => ['required', 'string', 'max:255'],
            'unidade_medida' => ['required', 'string', 'max:20', 'exists:unidades_medida,sigla'],
            'preco' => ['required', 'numeric', 'min:0'],
            'estoque' => ['required', 'integer', 'min:0'],
        ]);

        $data['descricao'] = preg_replace('/\s+/', ' ', trim((string) $data['descricao']));
        $data['unidade_medida'] = strtoupper(trim((string) $data['unidade_medida']));

        $itemDuplicado = DB::table('itens')
            ->where('id', '!=', $id)
            ->whereRaw('LOWER(descricao) = ?', [Str::lower($data['descricao'])])
            ->where('unidade_medida', $data['unidade_medida'])
            ->exists();

        if ($itemDuplicado) {
            return response()->json([
                'message' => 'Ja existe item com esta descricao e unidade de medida.',
            ], 422);
        }

        DB::table('itens')
            ->where('id', $id)
            ->update([
                'codigo' => trim((string) $data['codigo']),
                'descricao' => $data['descricao'],
                'unidade_medida' => $data['unidade_medida'],
                'preco' => $data['preco'],
                'estoque' => $data['estoque'],
                'updated_at' => now(),
            ]);

        return response()->json([
            'message' => 'Item atualizado com sucesso.',
        ]);
    });

    Route::delete('/itens/{id}', function (int $id) use ($ensureItensTable, $itemEstaEmUso) {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        $ensureItensTable();

        $item = DB::table('itens')->where('id', $id)->first();

        if (! $item) {
            return response()->json([
                'message' => 'Item nao encontrado.',
            ], 404);
        }

        if ($itemEstaEmUso((int) $item->id, (string) $item->codigo)) {
            return response()->json([
                'message' => 'Nao e permitido excluir item ja utilizado em outros registros.',
            ], 422);
        }

        DB::table('itens')
            ->where('id', $id)
            ->delete();

        return response()->json([
            'message' => 'Item excluido com sucesso.',
        ]);
    });

    Route::get('/unidades-medida', function () use ($ensureUnidadesMedidaTable) {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        $ensureUnidadesMedidaTable();

        $items = DB::table('unidades_medida as um')
            ->select('um.id', 'um.sigla', 'um.descricao')
            ->selectRaw('EXISTS (SELECT 1 FROM itens i WHERE i.unidade_medida = um.sigla) as em_uso')
            ->orderBy('sigla')
            ->get();

        return response()->json([
            'items' => $items,
        ]);
    });

    Route::post('/unidades-medida', function (Request $request) use ($ensureUnidadesMedidaTable) {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        $ensureUnidadesMedidaTable();

        $sigla = strtoupper(trim((string) $request->input('sigla', '')));
        $request->merge([
            'sigla' => $sigla,
        ]);

        $data = $request->validate([
            'sigla' => ['required', 'string', 'max:20', 'unique:unidades_medida,sigla'],
            'descricao' => ['nullable', 'string', 'max:100'],
        ]);

        $descricaoNormalizada = preg_replace('/\s+/', ' ', trim((string) ($data['descricao'] ?? '')));
        $data['descricao'] = $descricaoNormalizada !== '' ? $descricaoNormalizada : null;

        if ($data['descricao']) {
            $descricaoDuplicada = DB::table('unidades_medida')
                ->whereRaw('LOWER(descricao) = ?', [Str::lower($data['descricao'])])
                ->exists();

            if ($descricaoDuplicada) {
                return response()->json([
                    'message' => 'Ja existe unidade de medida com esta descricao.',
                ], 422);
            }
        }

        DB::table('unidades_medida')->insert([
            'sigla' => $data['sigla'],
            'descricao' => $data['descricao'] ?: null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Unidade de medida salva com sucesso.',
        ], 201);
    });

    Route::put('/unidades-medida/{id}', function (Request $request, int $id) use ($ensureUnidadesMedidaTable) {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        $ensureUnidadesMedidaTable();

        $unidade = DB::table('unidades_medida')->where('id', $id)->first();

        if (! $unidade) {
            return response()->json([
                'message' => 'Unidade de medida nao encontrada.',
            ], 404);
        }

        $emUso = DB::table('itens')
            ->where('unidade_medida', $unidade->sigla)
            ->exists();

        if ($emUso) {
            return response()->json([
                'message' => 'Nao e permitido editar unidade de medida ja utilizada em itens.',
            ], 422);
        }

        $sigla = strtoupper(trim((string) $request->input('sigla', '')));
        $request->merge([
            'sigla' => $sigla,
        ]);

        $data = $request->validate([
            'sigla' => ['required', 'string', 'max:20', 'unique:unidades_medida,sigla,' . $id],
            'descricao' => ['nullable', 'string', 'max:100'],
        ]);

        $descricaoNormalizada = preg_replace('/\s+/', ' ', trim((string) ($data['descricao'] ?? '')));
        $data['descricao'] = $descricaoNormalizada !== '' ? $descricaoNormalizada : null;

        if ($data['descricao']) {
            $descricaoDuplicada = DB::table('unidades_medida')
                ->where('id', '!=', $id)
                ->whereRaw('LOWER(descricao) = ?', [Str::lower($data['descricao'])])
                ->exists();

            if ($descricaoDuplicada) {
                return response()->json([
                    'message' => 'Ja existe unidade de medida com esta descricao.',
                ], 422);
            }
        }

        DB::table('unidades_medida')
            ->where('id', $id)
            ->update([
                'sigla' => $data['sigla'],
                'descricao' => $data['descricao'] ?: null,
                'updated_at' => now(),
            ]);

        return response()->json([
            'message' => 'Unidade de medida atualizada com sucesso.',
        ]);
    });

    Route::delete('/unidades-medida/{id}', function (int $id) use ($ensureUnidadesMedidaTable) {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        $ensureUnidadesMedidaTable();

        $unidade = DB::table('unidades_medida')->where('id', $id)->first();

        if (! $unidade) {
            return response()->json([
                'message' => 'Unidade de medida nao encontrada.',
            ], 404);
        }

        $emUso = DB::table('itens')
            ->where('unidade_medida', $unidade->sigla)
            ->exists();

        if ($emUso) {
            return response()->json([
                'message' => 'Nao e permitido excluir unidade de medida ja utilizada em itens.',
            ], 422);
        }

        DB::table('unidades_medida')
            ->where('id', $id)
            ->delete();

        return response()->json([
            'message' => 'Unidade de medida excluida com sucesso.',
        ]);
    });
});
