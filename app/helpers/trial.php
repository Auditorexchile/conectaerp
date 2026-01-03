<?php
/**
 * CONECTA ERP - HELPER DE TRIAL
 * Gestión del período de prueba de 14 días
 */

class TrialHelper
{
    /**
     * Verificar si la empresa está en período de trial
     */
    public static function isInTrial($empresaId)
    {
        $empresa = db()->selectOne(
            "SELECT en_trial, fecha_inicio_trial, fecha_fin_trial, suscripcion_activa
             FROM empresas
             WHERE id = :id",
            ['id' => $empresaId]
        );

        if (!$empresa) {
            return false;
        }

        return $empresa['en_trial'] == 1;
    }

    /**
     * Verificar si el trial ha expirado
     */
    public static function hasTrialExpired($empresaId)
    {
        $empresa = db()->selectOne(
            "SELECT en_trial, fecha_fin_trial
             FROM empresas
             WHERE id = :id",
            ['id' => $empresaId]
        );

        if (!$empresa || $empresa['en_trial'] != 1) {
            return false;
        }

        if (empty($empresa['fecha_fin_trial'])) {
            return false;
        }

        $fechaFin = new DateTime($empresa['fecha_fin_trial']);
        $ahora = new DateTime();

        return $ahora > $fechaFin;
    }

    /**
     * Obtener días restantes de trial
     */
    public static function getDaysRemaining($empresaId)
    {
        $empresa = db()->selectOne(
            "SELECT en_trial, fecha_inicio_trial, fecha_fin_trial
             FROM empresas
             WHERE id = :id",
            ['id' => $empresaId]
        );

        if (!$empresa || $empresa['en_trial'] != 1) {
            return null;
        }

        if (empty($empresa['fecha_fin_trial'])) {
            return 14; // Si no tiene fecha fin, asumimos 14 días
        }

        $fechaFin = new DateTime($empresa['fecha_fin_trial']);
        $ahora = new DateTime();

        if ($ahora > $fechaFin) {
            return 0; // Trial expirado
        }

        $diff = $ahora->diff($fechaFin);
        return $diff->days;
    }

    /**
     * Crear trial automáticamente para una empresa
     */
    public static function createTrial($empresaId)
    {
        // Verificar si ya tiene trial
        $trialExistente = db()->selectOne(
            "SELECT id FROM trial_log WHERE empresa_id = :id ORDER BY id DESC LIMIT 1",
            ['id' => $empresaId]
        );

        if ($trialExistente) {
            return false; // Ya tiene trial
        }

        $fechaInicio = date('Y-m-d');
        $fechaFin = date('Y-m-d', strtotime('+14 days'));

        // Crear registro en trial_log
        db()->insert('trial_log', [
            'empresa_id' => $empresaId,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'dias_totales' => 14,
            'activo' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Actualizar empresa
        db()->update('empresas', [
            'en_trial' => 1,
            'fecha_inicio_trial' => $fechaInicio,
            'fecha_fin_trial' => $fechaFin,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = :id', ['id' => $empresaId]);

        return true;
    }

    /**
     * Verificar y bloquear si el trial expiró
     * Retorna true si está bloqueado
     */
    public static function checkAndBlock($empresaId)
    {
        if (!self::isInTrial($empresaId)) {
            return false; // No está en trial, no bloqueamos
        }

        if (self::hasTrialExpired($empresaId)) {
            // Marcar trial como inactivo
            db()->query(
                "UPDATE trial_log SET activo = 0 WHERE empresa_id = :id",
                ['id' => $empresaId]
            );

            // Marcar empresa como trial expirado
            db()->update('empresas', [
                'en_trial' => 0,
                'suscripcion_activa' => 0,
                'bloqueada_por_pago' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = :id', ['id' => $empresaId]);

            return true; // Bloqueado
        }

        return false; // No bloqueado
    }

    /**
     * Obtener información completa del trial
     */
    public static function getTrialInfo($empresaId)
    {
        $empresa = db()->selectOne(
            "SELECT en_trial, fecha_inicio_trial, fecha_fin_trial, suscripcion_activa
             FROM empresas
             WHERE id = :id",
            ['id' => $empresaId]
        );

        if (!$empresa) {
            return null;
        }

        $info = [
            'en_trial' => $empresa['en_trial'] == 1,
            'fecha_inicio' => $empresa['fecha_inicio_trial'],
            'fecha_fin' => $empresa['fecha_fin_trial'],
            'dias_restantes' => self::getDaysRemaining($empresaId),
            'expirado' => self::hasTrialExpired($empresaId),
            'suscripcion_activa' => $empresa['suscripcion_activa'] == 1,
        ];

        // Calcular porcentaje de uso del trial
        if ($info['en_trial'] && !empty($empresa['fecha_inicio_trial']) && !empty($empresa['fecha_fin_trial'])) {
            $inicio = new DateTime($empresa['fecha_inicio_trial']);
            $fin = new DateTime($empresa['fecha_fin_trial']);
            $ahora = new DateTime();

            $totalDias = $inicio->diff($fin)->days;
            $diasTranscurridos = $inicio->diff($ahora)->days;

            $info['dias_totales'] = $totalDias;
            $info['dias_transcurridos'] = min($diasTranscurridos, $totalDias);
            $info['porcentaje_usado'] = $totalDias > 0 ? round(($diasTranscurridos / $totalDias) * 100, 1) : 0;
        } else {
            $info['dias_totales'] = 14;
            $info['dias_transcurridos'] = 0;
            $info['porcentaje_usado'] = 0;
        }

        return $info;
    }

    /**
     * Renderizar banner de trial
     */
    public static function renderBanner($empresaId)
    {
        $info = self::getTrialInfo($empresaId);

        if (!$info || !$info['en_trial']) {
            return ''; // No mostrar banner si no está en trial
        }

        $diasRestantes = $info['dias_restantes'];
        $porcentaje = $info['porcentaje_usado'];

        // Determinar color según días restantes
        if ($diasRestantes <= 3) {
            $color = '#ef4444'; // Rojo
            $bgColor = 'rgba(239, 68, 68, 0.1)';
            $icon = '⚠️';
            $urgencia = '¡URGENTE!';
        } elseif ($diasRestantes <= 7) {
            $color = '#f59e0b'; // Naranja
            $bgColor = 'rgba(245, 158, 11, 0.1)';
            $icon = '⏰';
            $urgencia = 'Importante:';
        } else {
            $color = '#3b82f6'; // Azul
            $bgColor = 'rgba(59, 130, 246, 0.1)';
            $icon = 'ℹ️';
            $urgencia = '';
        }

        $html = <<<HTML
<div class="trial-banner" style="background: {$bgColor}; border-left: 4px solid {$color}; padding: 1rem 1.5rem; margin-bottom: 1.5rem; border-radius: 8px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div style="display: flex; align-items: center; gap: 1rem; flex: 1;">
        <span style="font-size: 1.5rem;">{$icon}</span>
        <div>
            <strong style="color: {$color}; font-size: 1rem;">{$urgencia} Período de prueba</strong>
            <p style="margin: 0.25rem 0 0 0; color: #374151; font-size: 0.875rem;">
                Te quedan <strong style="color: {$color};">{$diasRestantes} días</strong> de prueba gratuita.
                {$this->getTrialMessage($diasRestantes)}
            </p>
        </div>
    </div>
    <div style="display: flex; align-items: center; gap: 1rem;">
        <div style="text-align: center; min-width: 80px;">
            <div style="font-size: 1.5rem; font-weight: 700; color: {$color};">{$diasRestantes}</div>
            <div style="font-size: 0.75rem; color: #6b7280;">días restantes</div>
        </div>
        <a href="/app/router.php?module=planes" class="btn btn-primary" style="background: {$color}; color: white; padding: 0.5rem 1.25rem; border-radius: 6px; text-decoration: none; font-weight: 600; white-space: nowrap;">
            Ver planes
        </a>
    </div>
</div>
HTML;

        return $html;
    }

    /**
     * Obtener mensaje personalizado según días restantes
     */
    private static function getTrialMessage($dias)
    {
        if ($dias <= 1) {
            return 'Tu trial expira <strong>mañana</strong>. ¡Elige tu plan ahora para no perder acceso!';
        } elseif ($dias <= 3) {
            return 'Tu trial está por expirar. Elige un plan para continuar usando Conecta ERP.';
        } elseif ($dias <= 7) {
            return 'Elige tu plan ahora y continúa disfrutando de Conecta ERP.';
        } else {
            return 'Explora todas las funcionalidades sin compromiso.';
        }
    }

    /**
     * Extender trial (solo para admins)
     */
    public static function extendTrial($empresaId, $diasAdicionales)
    {
        $empresa = db()->selectOne(
            "SELECT fecha_fin_trial FROM empresas WHERE id = :id",
            ['id' => $empresaId]
        );

        if (!$empresa || empty($empresa['fecha_fin_trial'])) {
            return false;
        }

        $nuevaFechaFin = date('Y-m-d', strtotime($empresa['fecha_fin_trial'] . " +{$diasAdicionales} days"));

        db()->update('empresas', [
            'fecha_fin_trial' => $nuevaFechaFin,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = :id', ['id' => $empresaId]);

        // Actualizar trial_log
        db()->query(
            "UPDATE trial_log
             SET fecha_fin = :fecha_fin,
                 dias_totales = DATEDIFF(:fecha_fin2, fecha_inicio),
                 updated_at = :updated_at
             WHERE empresa_id = :empresa_id
             ORDER BY id DESC
             LIMIT 1",
            [
                'fecha_fin' => $nuevaFechaFin,
                'fecha_fin2' => $nuevaFechaFin,
                'updated_at' => date('Y-m-d H:i:s'),
                'empresa_id' => $empresaId
            ]
        );

        return true;
    }

    /**
     * Convertir trial a suscripción paga
     */
    public static function convertToSubscription($empresaId, $planId)
    {
        // Marcar trial como inactivo
        db()->query(
            "UPDATE trial_log SET activo = 0 WHERE empresa_id = :id",
            ['id' => $empresaId]
        );

        // Actualizar empresa
        db()->update('empresas', [
            'en_trial' => 0,
            'plan_id' => $planId,
            'suscripcion_activa' => 1,
            'bloqueada_por_pago' => 0,
            'fecha_activacion' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = :id', ['id' => $empresaId]);

        return true;
    }
}
