<?php

session_start();

require '../inc/db.php';

include '../inc/layout/header.php';
include '../inc/layout/sidebar.php';

?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-12">
                    <h1 class="text-center">Nuevo Plato</h1>
                </div>

            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">

                <div class="col-md-8 col-lg-6">

                    <form id="formNuevoPlato">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Nombres del Plato</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="nombre_es">Nombre (Español)</label>
                                    <input type="text" name="nombre_es" id="nombre_es" class="form-control" placeholder="Ej: Paella mixta">
                                </div>
                                <div class="form-group">
                                    <label for="nombre_en">Nombre (Inglés)</label>
                                    <input type="text" name="nombre_en" id="nombre_en" class="form-control" placeholder="Ej: Mixed Paella">
                                </div>
                                <div class="form-group">
                                    <label for="nombre_fr">Nombre (Francés)</label>
                                    <input type="text" name="nombre_fr" id="nombre_fr" class="form-control" placeholder="Ej: Paella mixte">
                                </div>
                            </div>
                        </div>

                        <div class="card card-secondary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Ubicación en Buffet</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Mesa</label>
                                            <select name="mesa_id" class="form-control">
                                                <option value="1">Platos Calientes</option>
                                                <option value="2">Ensaladas</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Posición</label>
                                            <input type="text" name="posicion" class="form-control" placeholder="Ej: 03">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row pb-5">
                            <div class="col-12 text-right">
                                <a href="gestion_platos.php" class="btn btn-default">Cancelar</a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Guardar Plato
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>
</div>










<?php
include '../inc/layout/footer.php';
