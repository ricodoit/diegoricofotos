<?php
// [source: 21]
ini_set('display_errors', 0);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php';

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) { 
    header('Location: index.php'); 
    exit; 
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: admin.php');
    exit;
}

// OBTENER DATOS DE LA GALERÍA
$stmt = $db->prepare("SELECT * FROM clientes_areaclientes WHERE id = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    header('Location: admin.php');
    exit;
}

// CARPETAS DISPONIBLES EN ESTA GALERÍA PARA EL SELECTOR
$stmt_c = $db->prepare("SELECT DISTINCT carpeta FROM fotos WHERE cliente_id = ? ORDER BY carpeta ASC");
$stmt_c->execute([$id]);
$lista_carpetas_db = $stmt_c->fetchAll(PDO::FETCH_COLUMN);
if (empty($lista_carpetas_db)) {
    $lista_carpetas_db = ['Única'];
}

// ORGANIZAR FOTO A NUEVA CARPETA (ACCIÓN AJAX / POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mover_foto_carpeta'])) {
    $nombre_foto_objetivo = trim($_POST['nombre_archivo']);
    $nueva_carpeta = trim($_POST['nueva_carpeta']);

    if (!empty($nombre_foto_objetivo) && !empty($nueva_carpeta)) {
        // Actualizamos la carpeta de la foto en la base de datos buscando por coincidencia en la ruta
        $db->prepare("UPDATE fotos SET carpeta = ? WHERE cliente_id = ? AND (ruta_original LIKE ? OR ruta_marca_agua LIKE ?)")
           ->execute([$nueva_carpeta, $id, '%' . $nombre_foto_objetivo . '%', '%' . $nombre_foto_objetivo . '%']);
    }
    header("Location: mercadillo.php?id=$id");
    exit;
}

$nombre_galeria = $cliente['nombre_galeria'] ?? $cliente['nombre'] ?? 'Sin nombre';

// ELIMINAR PEDIDO
if (isset($_GET['eliminar_pedido'])) {
    $id_pedido = intval($_GET['eliminar_pedido']);
    $db->prepare("DELETE FROM pedidos WHERE id = ? AND cliente_id = ?")->execute([$id_pedido, $id]);
    header("Location: mercadillo.php?id=$id");
    exit;
}

// MODIFICAR PEDIDO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_pedido'])) {
    $id_pedido = intval($_POST['pedido_id']);
    $nuevo_nombre = trim($_POST['nombre_familia']);
    $nuevo_tel = trim($_POST['telefono']);
    $nuevo_total = floatval($_POST['total_pagar']);

    $db->prepare("UPDATE pedidos SET nombre_familia = ?, telefono = ?, total_pagar = ? WHERE id = ? AND cliente_id = ?")
       ->execute([$nuevo_nombre, $nuevo_tel, $nuevo_total, $id_pedido, $id]);
       
    header("Location: mercadillo.php?id=$id");
    exit;
}

$pedidos = $db->prepare("SELECT * FROM pedidos WHERE cliente_id = ? ORDER BY id DESC");
$pedidos->execute([$id]);
$lista_pedidos = $pedidos->fetchAll(PDO::FETCH_ASSOC);

// Calcular totales para la cabecera
$total_recaudado = 0;
foreach($lista_pedidos as $p) {
    $total_recaudado += floatval($p['total_pagar']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Pedidos | <?=htmlspecialchars($nombre_galeria)?></title>
    <link rel="icon" type="image/png" href="../img/LOGOS/logodiegoricofotos_negro.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> 
        body { font-family: 'Plus Jakarta Sans', sans-serif; } 
        @media (max-width: 640px) {
            input, select, textarea { font-size: 16px !important; }
        }
        @media print {
            nav, .no-print, button, a.no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
            .shadow-sm, .shadow-xs, .shadow-2xl {
                box-shadow: none !important;
            }
            .bg-slate-50 {
                background: white !important;
            }
            .border {
                border-color: #cbd5e1 !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col pb-24">

    <!-- HEADER UNIFICADO -->
    <nav class="bg-blue-900 border-b border-blue-800 px-4 md:px-8 py-3.5 shadow-sm sticky top-0 z-40">
        <div class="flex justify-between items-center gap-3">
            <div class="flex items-center gap-3">
                <span class="text-base sm:text-lg md:text-xl font-bold text-white tracking-wide">diegoricofotos</span>
                <span class="bg-blue-800 text-blue-200 text-[10px] font-bold uppercase px-3 py-1 rounded-full border border-blue-700 hidden sm:inline-block">Revisión de Pedidos: <?=htmlspecialchars($nombre_galeria)?></span>
            </div>
            
            <div class="flex items-center gap-2 sm:gap-3">
                <button onclick="window.print()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 sm:px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                    <i class="fa-solid fa-file-pdf"></i> <span class="hidden sm:inline">Imprimir / </span>PDF
                </button>
                <a href="admin.php" class="bg-blue-800/60 hover:bg-blue-800 text-blue-200 hover:text-white px-3.5 sm:px-4 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-3 sm:px-6 py-6 md:py-10 space-y-6 md:space-y-8 flex-grow w-full">
        
        <!-- TARJETAS DE RESUMEN RÁPIDO -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white border border-slate-200 p-5 sm:p-6 rounded-3xl shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] uppercase font-bold text-slate-400 tracking-wider">Total Pedidos</span>
                    <h2 class="text-2xl font-bold text-slate-900 mt-1"><?=count($lista_pedidos)?></h2>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-xl font-bold">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
            </div>

            <div class="bg-white border border-slate-200 p-5 sm:p-6 rounded-3xl shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] uppercase font-bold text-slate-400 tracking-wider">Recaudación Estimada</span>
                    <h2 class="text-2xl font-bold text-emerald-600 mt-1"><?=number_format($total_recaudado, 2, ',', '.')?> €</h2>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-xl font-bold">
                    <i class="fa-solid fa-euro-sign"></i>
                </div>
            </div>
        </div>

        <!-- LISTADO DE PEDIDOS -->
        <div class="bg-white border border-slate-200 p-5 sm:p-8 rounded-3xl shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-cart-shopping text-blue-600"></i> Listado de Pedidos Finalizados y Organización de Fotos
                </h2>
                <span class="text-xs text-slate-400 font-bold shrink-0"><?=count($lista_pedidos)?> reg.</span>
            </div>

            <?php if(empty($lista_pedidos)): ?>
                <p class="text-slate-400 text-xs italic py-12 text-center">No hay ningún pedido registrado todavía para esta galería.</p>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach($lista_pedidos as $p): 
                        $fotos_data = json_decode($p['fotos_ids'], true);
                        $tel_limpio = preg_replace('/[^0-9]/', '', $p['telefono']);
                    ?>
                    <div class="bg-slate-50 border border-slate-200 p-4 sm:p-6 rounded-2xl space-y-4 shadow-2xs">
                        
                        <!-- CABECERA DEL PEDIDO -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-200 pb-4">
                            <div>
                                <h3 class="font-bold text-slate-900 text-base"><?=htmlspecialchars($p['nombre_familia'])?></h3>
                                <p class="text-xs text-slate-500 mt-1 flex items-center gap-2 font-medium flex-wrap">
                                    <i class="fa-solid fa-phone text-blue-600"></i> <span><?=htmlspecialchars($p['telefono'])?></span>
                                    <span class="text-slate-300">|</span>
                                    <i class="fa-regular fa-clock"></i> <span><?=$p['fecha']?></span>
                                </p>
                            </div>
                            
                            <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                                <a href="https://wa.me/<?=$tel_limpio?>?text=Hola,%20te%20escribo%20desde%20Diego%20Rico%20Fotos%20en%20relación%20a%20tu%20pedido:" target="_blank" class="no-print bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                                    <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp
                                </a>

                                <div class="bg-white border border-slate-200 px-4 py-1.5 rounded-xl text-right shadow-2xs">
                                    <span class="text-[9px] uppercase font-bold block text-slate-400">Total Importe</span>
                                    <span class="text-base sm:text-lg font-bold text-emerald-600"><?=$p['total_pagar']?> €</span>
                                </div>
                            </div>
                        </div>

                        <!-- FOTOS Y OPCIÓN DE CAMBIAR DE CARPETA -->
                        <div class="space-y-2">
                            <p class="text-xs uppercase font-bold text-slate-600 tracking-wider">Referencias, Formatos y Reasignación de Carpetas:</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                <?php if(is_array($fotos_data)): ?>
                                    <?php foreach($fotos_data as $nombre_foto => $info_formato): 
                                        $stmt_c = $db->prepare("SELECT carpeta FROM fotos WHERE cliente_id = ? AND (ruta_original LIKE ? OR ruta_marca_agua LIKE ?)");
                                        $stmt_c->execute([$id, '%' . $nombre_foto . '%', '%' . $nombre_foto . '%']);
                                        $res_c = $stmt_c->fetch(PDO::FETCH_ASSOC);
                                        $carpeta_foto = $res_c['carpeta'] ?? 'Única';

                                        $texto_tipo = "";
                                        $badge_color = "bg-slate-100 text-slate-600";
                                        
                                        if (is_array($info_formato)) {
                                            $papel = isset($info_formato['papel']) && $info_formato['papel'];
                                            $digital = isset($info_formato['digital']) && $info_formato['digital'];
                                            
                                            if ($papel && $digital) {
                                                $texto_tipo = "🌟 Pack (Digital + Papel)";
                                                $badge_color = "bg-purple-50 text-purple-700 border border-purple-200";
                                            } elseif ($papel) {
                                                $texto_tipo = "🖨️ Solo Papel";
                                                $badge_color = "bg-blue-50 text-blue-700 border border-blue-200";
                                            } elseif ($digital) {
                                                $texto_tipo = "💻 Solo Digital";
                                                $badge_color = "bg-emerald-50 text-emerald-700 border border-emerald-200";
                                            }
                                        } else {
                                            $texto_tipo = ucfirst($info_formato);
                                            $badge_color = "bg-slate-100 text-slate-600";
                                        }
                                    ?>
                                        <div class="bg-white border border-slate-200 p-3.5 rounded-2xl space-y-2.5 shadow-2xs flex flex-col justify-between">
                                            <div class="space-y-1">
                                                <span class="text-xs font-mono font-bold text-slate-800 truncate block" title="<?=htmlspecialchars($nombre_foto)?>">
                                                    <i class="fa-regular fa-image text-blue-600 mr-1"></i> <?=htmlspecialchars($nombre_foto)?>
                                                </span>
                                                <div class="flex justify-between items-center text-[11px] font-medium pt-1">
                                                    <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded font-bold">📁 <?=$carpeta_foto?></span>
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold <?=$badge_color?>"><?=$texto_tipo?></span>
                                                </div>
                                            </div>

                                            <!-- SELECTOR PARA MOVER A OTRA CARPETA AL INSTANTE -->
                                            <div class="no-print pt-2 border-t border-slate-100">
                                                <form method="POST" class="flex items-center gap-1.5">
                                                    <input type="hidden" name="mover_foto_carpeta" value="1">
                                                    <input type="hidden" name="nombre_archivo" value="<?=htmlspecialchars($nombre_foto)?>">
                                                    
                                                    <select name="nueva_carpeta" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2 text-xs font-semibold text-slate-700 outline-none focus:ring-1 focus:ring-blue-500 cursor-pointer">
                                                        <option value="" disabled selected>Mover a carpeta...</option>
                                                        <?php foreach($lista_carpetas_db as $cf): ?>
                                                            <option value="<?=htmlspecialchars($cf)?>"><?=htmlspecialchars($cf)?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-xs text-slate-400 italic">No hay detalles de archivos disponibles.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- BOTONES DE ACCIÓN (MODIFICAR Y ELIMINAR) -->
                        <div class="no-print flex flex-col sm:flex-row justify-end gap-2 pt-3 border-t border-slate-200">
                            <button onclick="abrirModalEditar(<?=$p['id']?>, '<?=htmlspecialchars($p['nombre_familia'], ENT_QUOTES)?>', '<?=htmlspecialchars($p['telefono'], ENT_QUOTES)?>', '<?=$p['total_pagar']?>')" class="w-full sm:w-auto bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 px-4 py-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs">
                                <i class="fa-solid fa-pen-to-square"></i> Modificar
                            </button>
                            <a href="mercadillo.php?id=<?=$id?>&eliminar_pedido=<?=$p['id']?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este pedido por completo?')" class="w-full sm:w-auto bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 px-4 py-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs">
                                <i class="fa-solid fa-trash"></i> Eliminar
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- MODAL MODIFICAR PEDIDO -->
    <div id="modalEditar" class="no-print fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[100] hidden flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white border border-slate-200 p-6 sm:p-8 rounded-3xl max-w-md w-full relative shadow-2xl space-y-5 my-auto">
            <button onclick="document.getElementById('modalEditar').classList.add('hidden')" class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 cursor-pointer"><i class="fa-solid fa-xmark text-xl"></i></button>
            
            <h2 class="text-xl font-bold text-slate-900"><i class="fa-solid fa-pen-to-square text-blue-600 mr-2"></i> Modificar Pedido</h2>
            
            <form method="POST" class="space-y-4">
                <input type="hidden" name="editar_pedido" value="1">
                <input type="hidden" name="pedido_id" id="edit_pedido_id">
                
                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Datos del Cliente / Ticket</label>
                    <input type="text" name="nombre_familia" id="edit_nombre" required class="w-full bg-slate-50 border border-slate-300 p-3.5 rounded-xl text-sm font-semibold text-slate-800 outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Teléfono</label>
                    <input type="text" name="telefono" id="edit_telefono" required class="w-full bg-slate-50 border border-slate-300 p-3.5 rounded-xl text-sm font-semibold text-slate-800 outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Importe Total (€)</label>
                    <input type="number" step="0.01" name="total_pagar" id="edit_total" required class="w-full bg-slate-50 border border-slate-300 p-3.5 rounded-xl text-sm font-semibold text-slate-800 outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-xl font-bold uppercase text-xs transition shadow-sm tracking-widest mt-2 cursor-pointer">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <script>
        function abrirModalEditar(id, nombre, telefono, total) {
            document.getElementById('edit_pedido_id').value = id;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_telefono').value = telefono;
            document.getElementById('edit_total').value = total;
            document.getElementById('modalEditar').classList.remove('hidden');
        }
    </script>

</body>
</html>