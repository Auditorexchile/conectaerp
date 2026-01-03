'use client';

import { useState } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { validateRut, formatRut } from '@/utils/rutValidator';

export default function LoginPage() {
  const router = useRouter();
  const [showPassword, setShowPassword] = useState(false);
  const [formData, setFormData] = useState({
    rutEmpresa: '',
    email: '',
    password: '',
  });
  const [errors, setErrors] = useState<{
    rutEmpresa?: string;
    email?: string;
    password?: string;
    general?: string;
  }>({});
  const [loading, setLoading] = useState(false);

  const handleRutChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value;
    setFormData({ ...formData, rutEmpresa: value });

    // Validar RUT en tiempo real
    if (value && !validateRut(value)) {
      setErrors({ ...errors, rutEmpresa: 'RUT inválido' });
    } else {
      const { rutEmpresa, ...rest } = errors;
      setErrors(rest);
    }
  };

  const handleRutBlur = () => {
    if (formData.rutEmpresa && validateRut(formData.rutEmpresa)) {
      setFormData({ ...formData, rutEmpresa: formatRut(formData.rutEmpresa) });
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setErrors({});
    setLoading(true);

    // Validaciones
    const newErrors: typeof errors = {};

    if (!formData.rutEmpresa) {
      newErrors.rutEmpresa = 'RUT de empresa es requerido';
    } else if (!validateRut(formData.rutEmpresa)) {
      newErrors.rutEmpresa = 'RUT inválido';
    }

    if (!formData.email) {
      newErrors.email = 'Email es requerido';
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
      newErrors.email = 'Email inválido';
    }

    if (!formData.password) {
      newErrors.password = 'Contraseña es requerida';
    }

    if (Object.keys(newErrors).length > 0) {
      setErrors(newErrors);
      setLoading(false);
      return;
    }

    // Aquí iría la llamada a la API
    setTimeout(() => {
      // Simulación de error para demo
      setErrors({ general: 'Credenciales incorrectas. Verifica tu información.' });
      setLoading(false);
    }, 1000);
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col">
      {/* Header simple */}
      <div className="p-6">
        <Link href="/" className="flex items-center space-x-2">
          <div className="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
            <span className="text-white font-bold text-xl">C</span>
          </div>
          <span className="font-bold text-xl text-gray-900">Conecta ERP</span>
        </Link>
      </div>

      {/* Card de Login */}
      <div className="flex-1 flex items-center justify-center px-4 py-12">
        <div className="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
          <div className="text-center mb-8">
            <h1 className="text-3xl font-bold text-gray-900 mb-2">Iniciar sesión</h1>
            <p className="text-gray-600">Accede a tu cuenta de Conecta ERP</p>
          </div>

          {errors.general && (
            <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
              <p className="text-sm text-red-600">{errors.general}</p>
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-6">
            {/* RUT Empresa */}
            <div>
              <label htmlFor="rutEmpresa" className="block text-sm font-medium text-gray-700 mb-2">
                RUT Empresa
              </label>
              <input
                type="text"
                id="rutEmpresa"
                value={formData.rutEmpresa}
                onChange={handleRutChange}
                onBlur={handleRutBlur}
                placeholder="12.345.678-9"
                className={`w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition ${
                  errors.rutEmpresa ? 'border-red-500' : 'border-gray-300'
                }`}
              />
              {errors.rutEmpresa && (
                <p className="mt-1 text-sm text-red-600">{errors.rutEmpresa}</p>
              )}
            </div>

            {/* Email */}
            <div>
              <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-2">
                Email de acceso
              </label>
              <input
                type="email"
                id="email"
                value={formData.email}
                onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                placeholder="tu@email.com"
                className={`w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition ${
                  errors.email ? 'border-red-500' : 'border-gray-300'
                }`}
              />
              {errors.email && (
                <p className="mt-1 text-sm text-red-600">{errors.email}</p>
              )}
            </div>

            {/* Contraseña */}
            <div>
              <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-2">
                Contraseña
              </label>
              <div className="relative">
                <input
                  type={showPassword ? 'text' : 'password'}
                  id="password"
                  value={formData.password}
                  onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                  placeholder="••••••••"
                  className={`w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition ${
                    errors.password ? 'border-red-500' : 'border-gray-300'
                  }`}
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                >
                  {showPassword ? '👁️' : '👁️‍🗨️'}
                </button>
              </div>
              {errors.password && (
                <p className="mt-1 text-sm text-red-600">{errors.password}</p>
              )}
            </div>

            {/* Olvidaste contraseña */}
            <div className="text-right">
              <Link href="/recuperar-contrasena" className="text-sm text-primary-600 hover:text-primary-700">
                ¿Olvidaste tu contraseña?
              </Link>
            </div>

            {/* Botón Ingresar */}
            <button
              type="submit"
              disabled={loading}
              className="w-full py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {loading ? 'Ingresando...' : 'Ingresar'}
            </button>
          </form>

          {/* Crear cuenta */}
          <div className="mt-6 text-center">
            <p className="text-gray-600">
              ¿No tienes cuenta?{' '}
              <Link href="/register" className="text-primary-600 hover:text-primary-700 font-semibold">
                Crear cuenta
              </Link>
            </p>
          </div>
        </div>
      </div>

      {/* Footer simple */}
      <div className="p-6 text-center text-sm text-gray-600">
        <p>© {new Date().getFullYear()} Conecta ERP - Todos los derechos reservados</p>
      </div>
    </div>
  );
}
