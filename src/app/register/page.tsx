'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';
import { useSearchParams } from 'next/navigation';
import { validateRut, formatRut } from '@/utils/rutValidator';

type Plan = 'starter' | 'profesional' | 'empresa' | 'corporativo';

interface FormData {
  // Sección 1 - Datos Empresa
  rutEmpresa: string;
  razonSocial: string;
  nombreFantasia: string;
  giro: string;
  direccion: string;
  // Sección 2 - Representante Legal
  nombreCompleto: string;
  rutRepresentante: string;
  email: string;
  telefono: string;
  // Sección 3 - Seguridad
  password: string;
  confirmPassword: string;
  // Sección 4 - Plan
  plan: Plan;
  // Sección 5 - Confirmación
  aceptaTerminos: boolean;
  aceptaPrivacidad: boolean;
}

const planes = {
  starter: {
    nombre: 'Starter',
    precio: 'Gratis',
    descripcion: '1 empresa, 1 usuario, facturación e inventario básico',
  },
  profesional: {
    nombre: 'Profesional',
    precio: '$49.990/mes',
    descripcion: '1 empresa, 5 usuarios, contabilidad completa',
  },
  empresa: {
    nombre: 'Empresa',
    precio: '$99.990/mes',
    descripcion: '1 empresa, usuarios ilimitados, producción y costos',
  },
  corporativo: {
    nombre: 'Corporativo',
    precio: 'Contactar',
    descripcion: 'Multiempresa, multi sucursal, BI e integraciones',
  },
};

export default function RegisterPage() {
  const searchParams = useSearchParams();
  const planFromUrl = searchParams.get('plan') as Plan | null;

  const [currentSection, setCurrentSection] = useState(1);
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [loading, setLoading] = useState(false);

  const [formData, setFormData] = useState<FormData>({
    rutEmpresa: '',
    razonSocial: '',
    nombreFantasia: '',
    giro: '',
    direccion: '',
    nombreCompleto: '',
    rutRepresentante: '',
    email: '',
    telefono: '',
    password: '',
    confirmPassword: '',
    plan: planFromUrl || 'starter',
    aceptaTerminos: false,
    aceptaPrivacidad: false,
  });

  const [errors, setErrors] = useState<Partial<Record<keyof FormData, string>>>({});

  // Calcular fortaleza de contraseña
  const getPasswordStrength = (password: string): 'weak' | 'medium' | 'strong' => {
    if (password.length < 6) return 'weak';
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;

    if (strength <= 1) return 'weak';
    if (strength <= 3) return 'medium';
    return 'strong';
  };

  const passwordStrength = getPasswordStrength(formData.password);

  const handleRutEmpresaChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value;
    setFormData({ ...formData, rutEmpresa: value });
  };

  const handleRutEmpresaBlur = () => {
    if (formData.rutEmpresa && validateRut(formData.rutEmpresa)) {
      setFormData({ ...formData, rutEmpresa: formatRut(formData.rutEmpresa) });
    }
  };

  const handleRutRepresentanteChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value;
    setFormData({ ...formData, rutRepresentante: value });
  };

  const handleRutRepresentanteBlur = () => {
    if (formData.rutRepresentante && validateRut(formData.rutRepresentante)) {
      setFormData({ ...formData, rutRepresentante: formatRut(formData.rutRepresentante) });
    }
  };

  const generateSecurePassword = () => {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    let password = '';
    for (let i = 0; i < 12; i++) {
      password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    setFormData({ ...formData, password, confirmPassword: password });
  };

  const validateSection = (section: number): boolean => {
    const newErrors: Partial<Record<keyof FormData, string>> = {};

    if (section === 1) {
      if (!formData.rutEmpresa) {
        newErrors.rutEmpresa = 'RUT de empresa es requerido';
      } else if (!validateRut(formData.rutEmpresa)) {
        newErrors.rutEmpresa = 'RUT inválido';
      }
      if (!formData.razonSocial) newErrors.razonSocial = 'Razón social es requerida';
      if (!formData.nombreFantasia) newErrors.nombreFantasia = 'Nombre fantasía es requerido';
      if (!formData.giro) newErrors.giro = 'Giro es requerido';
      if (!formData.direccion) newErrors.direccion = 'Dirección es requerida';
    }

    if (section === 2) {
      if (!formData.nombreCompleto) newErrors.nombreCompleto = 'Nombre completo es requerido';
      if (!formData.rutRepresentante) {
        newErrors.rutRepresentante = 'RUT del representante es requerido';
      } else if (!validateRut(formData.rutRepresentante)) {
        newErrors.rutRepresentante = 'RUT inválido';
      }
      if (!formData.email) {
        newErrors.email = 'Email es requerido';
      } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
        newErrors.email = 'Email inválido';
      }
      if (!formData.telefono) newErrors.telefono = 'Teléfono es requerido';
    }

    if (section === 3) {
      if (!formData.password) {
        newErrors.password = 'Contraseña es requerida';
      } else if (formData.password.length < 6) {
        newErrors.password = 'La contraseña debe tener al menos 6 caracteres';
      }
      if (!formData.confirmPassword) {
        newErrors.confirmPassword = 'Confirma tu contraseña';
      } else if (formData.password !== formData.confirmPassword) {
        newErrors.confirmPassword = 'Las contraseñas no coinciden';
      }
    }

    if (section === 5) {
      if (!formData.aceptaTerminos) newErrors.aceptaTerminos = 'Debes aceptar los términos';
      if (!formData.aceptaPrivacidad) newErrors.aceptaPrivacidad = 'Debes aceptar la política de privacidad';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const nextSection = () => {
    if (validateSection(currentSection)) {
      setCurrentSection(currentSection + 1);
    }
  };

  const prevSection = () => {
    setCurrentSection(currentSection - 1);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (validateSection(5)) {
      setLoading(true);
      // Aquí iría la llamada a la API
      console.log('Formulario enviado:', formData);
      setTimeout(() => {
        alert('Cuenta creada exitosamente!');
        setLoading(false);
      }, 2000);
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
      {/* Header simple */}
      <div className="p-6">
        <Link href="/" className="flex items-center space-x-2">
          <div className="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
            <span className="text-white font-bold text-xl">C</span>
          </div>
          <span className="font-bold text-xl text-gray-900">Conecta ERP</span>
        </Link>
      </div>

      {/* Formulario de registro */}
      <div className="container mx-auto px-4 py-8">
        <div className="max-w-3xl mx-auto">
          <div className="bg-white rounded-2xl shadow-2xl p-8">
            <div className="text-center mb-8">
              <h1 className="text-3xl font-bold text-gray-900 mb-2">Crear cuenta</h1>
              <p className="text-gray-600">Completa el formulario para comenzar</p>
            </div>

            {/* Barra de progreso */}
            <div className="mb-8">
              <div className="flex justify-between items-center mb-2">
                {[1, 2, 3, 4, 5].map((step) => (
                  <div
                    key={step}
                    className={`w-8 h-8 rounded-full flex items-center justify-center font-semibold ${
                      step === currentSection
                        ? 'bg-primary-600 text-white'
                        : step < currentSection
                        ? 'bg-green-500 text-white'
                        : 'bg-gray-200 text-gray-500'
                    }`}
                  >
                    {step < currentSection ? '✓' : step}
                  </div>
                ))}
              </div>
              <div className="h-2 bg-gray-200 rounded-full">
                <div
                  className="h-2 bg-primary-600 rounded-full transition-all duration-300"
                  style={{ width: `${(currentSection / 5) * 100}%` }}
                />
              </div>
              <div className="flex justify-between mt-2 text-xs text-gray-600">
                <span>Empresa</span>
                <span>Representante</span>
                <span>Seguridad</span>
                <span>Plan</span>
                <span>Confirmar</span>
              </div>
            </div>

            <form onSubmit={handleSubmit}>
              {/* SECCIÓN 1 - DATOS EMPRESA */}
              {currentSection === 1 && (
                <div className="space-y-6">
                  <h2 className="text-2xl font-bold text-gray-900 mb-4">Datos de la Empresa</h2>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      RUT Empresa *
                    </label>
                    <input
                      type="text"
                      value={formData.rutEmpresa}
                      onChange={handleRutEmpresaChange}
                      onBlur={handleRutEmpresaBlur}
                      placeholder="12.345.678-9"
                      className={`w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none ${
                        errors.rutEmpresa ? 'border-red-500' : 'border-gray-300'
                      }`}
                    />
                    {errors.rutEmpresa && (
                      <p className="mt-1 text-sm text-red-600">{errors.rutEmpresa}</p>
                    )}
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Razón Social *
                    </label>
                    <input
                      type="text"
                      value={formData.razonSocial}
                      onChange={(e) => setFormData({ ...formData, razonSocial: e.target.value })}
                      placeholder="Empresa S.A."
                      className={`w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none ${
                        errors.razonSocial ? 'border-red-500' : 'border-gray-300'
                      }`}
                    />
                    {errors.razonSocial && (
                      <p className="mt-1 text-sm text-red-600">{errors.razonSocial}</p>
                    )}
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Nombre Fantasía *
                    </label>
                    <input
                      type="text"
                      value={formData.nombreFantasia}
                      onChange={(e) => setFormData({ ...formData, nombreFantasia: e.target.value })}
                      placeholder="Mi Empresa"
                      className={`w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none ${
                        errors.nombreFantasia ? 'border-red-500' : 'border-gray-300'
                      }`}
                    />
                    {errors.nombreFantasia && (
                      <p className="mt-1 text-sm text-red-600">{errors.nombreFantasia}</p>
                    )}
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Giro *</label>
                    <input
                      type="text"
                      value={formData.giro}
                      onChange={(e) => setFormData({ ...formData, giro: e.target.value })}
                      placeholder="Servicios profesionales"
                      className={`w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none ${
                        errors.giro ? 'border-red-500' : 'border-gray-300'
                      }`}
                    />
                    {errors.giro && <p className="mt-1 text-sm text-red-600">{errors.giro}</p>)}
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Dirección Comercial *
                    </label>
                    <input
                      type="text"
                      value={formData.direccion}
                      onChange={(e) => setFormData({ ...formData, direccion: e.target.value })}
                      placeholder="Av. Principal 123, Santiago"
                      className={`w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none ${
                        errors.direccion ? 'border-red-500' : 'border-gray-300'
                      }`}
                    />
                    {errors.direccion && (
                      <p className="mt-1 text-sm text-red-600">{errors.direccion}</p>
                    )}
                  </div>
                </div>
              )}

              {/* SECCIÓN 2 - REPRESENTANTE LEGAL */}
              {currentSection === 2 && (
                <div className="space-y-6">
                  <h2 className="text-2xl font-bold text-gray-900 mb-4">Representante Legal</h2>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Nombre Completo *
                    </label>
                    <input
                      type="text"
                      value={formData.nombreCompleto}
                      onChange={(e) => setFormData({ ...formData, nombreCompleto: e.target.value })}
                      placeholder="Juan Pérez García"
                      className={`w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none ${
                        errors.nombreCompleto ? 'border-red-500' : 'border-gray-300'
                      }`}
                    />
                    {errors.nombreCompleto && (
                      <p className="mt-1 text-sm text-red-600">{errors.nombreCompleto}</p>
                    )}
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      RUT Representante *
                    </label>
                    <input
                      type="text"
                      value={formData.rutRepresentante}
                      onChange={handleRutRepresentanteChange}
                      onBlur={handleRutRepresentanteBlur}
                      placeholder="11.111.111-1"
                      className={`w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none ${
                        errors.rutRepresentante ? 'border-red-500' : 'border-gray-300'
                      }`}
                    />
                    {errors.rutRepresentante && (
                      <p className="mt-1 text-sm text-red-600">{errors.rutRepresentante}</p>
                    )}
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Email de acceso *
                    </label>
                    <input
                      type="email"
                      value={formData.email}
                      onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                      placeholder="juan@empresa.com"
                      className={`w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none ${
                        errors.email ? 'border-red-500' : 'border-gray-300'
                      }`}
                    />
                    {errors.email && <p className="mt-1 text-sm text-red-600">{errors.email}</p>}
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Teléfono *
                    </label>
                    <input
                      type="tel"
                      value={formData.telefono}
                      onChange={(e) => setFormData({ ...formData, telefono: e.target.value })}
                      placeholder="+56 9 1234 5678"
                      className={`w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none ${
                        errors.telefono ? 'border-red-500' : 'border-gray-300'
                      }`}
                    />
                    {errors.telefono && (
                      <p className="mt-1 text-sm text-red-600">{errors.telefono}</p>
                    )}
                  </div>
                </div>
              )}

              {/* SECCIÓN 3 - SEGURIDAD */}
              {currentSection === 3 && (
                <div className="space-y-6">
                  <h2 className="text-2xl font-bold text-gray-900 mb-4">Seguridad</h2>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Contraseña *
                    </label>
                    <div className="relative">
                      <input
                        type={showPassword ? 'text' : 'password'}
                        value={formData.password}
                        onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                        placeholder="••••••••"
                        className={`w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none ${
                          errors.password ? 'border-red-500' : 'border-gray-300'
                        }`}
                      />
                      <button
                        type="button"
                        onClick={() => setShowPassword(!showPassword)}
                        className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500"
                      >
                        {showPassword ? '👁️' : '👁️‍🗨️'}
                      </button>
                    </div>
                    {errors.password && (
                      <p className="mt-1 text-sm text-red-600">{errors.password}</p>
                    )}

                    {/* Indicador de fortaleza */}
                    {formData.password && (
                      <div className="mt-2">
                        <div className="flex items-center gap-2">
                          <div className="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div
                              className={`h-full transition-all ${
                                passwordStrength === 'weak'
                                  ? 'bg-red-500 w-1/3'
                                  : passwordStrength === 'medium'
                                  ? 'bg-yellow-500 w-2/3'
                                  : 'bg-green-500 w-full'
                              }`}
                            />
                          </div>
                          <span className="text-sm font-medium">
                            {passwordStrength === 'weak'
                              ? 'Débil'
                              : passwordStrength === 'medium'
                              ? 'Media'
                              : 'Fuerte'}
                          </span>
                        </div>
                      </div>
                    )}
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Confirmar Contraseña *
                    </label>
                    <div className="relative">
                      <input
                        type={showConfirmPassword ? 'text' : 'password'}
                        value={formData.confirmPassword}
                        onChange={(e) =>
                          setFormData({ ...formData, confirmPassword: e.target.value })
                        }
                        placeholder="••••••••"
                        className={`w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none ${
                          errors.confirmPassword ? 'border-red-500' : 'border-gray-300'
                        }`}
                      />
                      <button
                        type="button"
                        onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                        className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500"
                      >
                        {showConfirmPassword ? '👁️' : '👁️‍🗨️'}
                      </button>
                    </div>
                    {errors.confirmPassword && (
                      <p className="mt-1 text-sm text-red-600">{errors.confirmPassword}</p>
                    )}
                  </div>

                  <button
                    type="button"
                    onClick={generateSecurePassword}
                    className="text-sm text-primary-600 hover:text-primary-700 font-semibold"
                  >
                    🔑 Generar contraseña segura
                  </button>
                </div>
              )}

              {/* SECCIÓN 4 - PLAN */}
              {currentSection === 4 && (
                <div className="space-y-6">
                  <h2 className="text-2xl font-bold text-gray-900 mb-4">Selecciona tu Plan</h2>

                  <div className="space-y-4">
                    {(Object.keys(planes) as Plan[]).map((planKey) => (
                      <label
                        key={planKey}
                        className={`block p-4 border-2 rounded-lg cursor-pointer transition-all ${
                          formData.plan === planKey
                            ? 'border-primary-600 bg-primary-50'
                            : 'border-gray-200 hover:border-primary-300'
                        }`}
                      >
                        <input
                          type="radio"
                          name="plan"
                          value={planKey}
                          checked={formData.plan === planKey}
                          onChange={(e) =>
                            setFormData({ ...formData, plan: e.target.value as Plan })
                          }
                          className="mr-3"
                        />
                        <span className="font-bold text-lg">{planes[planKey].nombre}</span>
                        <span className="ml-2 text-primary-600 font-semibold">
                          {planes[planKey].precio}
                        </span>
                        <p className="text-sm text-gray-600 ml-6 mt-1">
                          {planes[planKey].descripcion}
                        </p>
                      </label>
                    ))}
                  </div>

                  {/* Resumen del plan */}
                  <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 className="font-semibold text-gray-900 mb-2">Resumen del plan:</h3>
                    <p className="text-sm text-gray-700">
                      <strong>{planes[formData.plan].nombre}</strong> -{' '}
                      {planes[formData.plan].precio}
                    </p>
                    <p className="text-sm text-gray-600 mt-1">
                      {planes[formData.plan].descripcion}
                    </p>
                  </div>
                </div>
              )}

              {/* SECCIÓN 5 - CONFIRMACIÓN */}
              {currentSection === 5 && (
                <div className="space-y-6">
                  <h2 className="text-2xl font-bold text-gray-900 mb-4">Confirmación</h2>

                  <div className="bg-gray-50 rounded-lg p-6 space-y-3">
                    <h3 className="font-semibold text-gray-900">Resumen de tu cuenta:</h3>
                    <div className="text-sm space-y-1">
                      <p>
                        <strong>Empresa:</strong> {formData.razonSocial}
                      </p>
                      <p>
                        <strong>RUT:</strong> {formData.rutEmpresa}
                      </p>
                      <p>
                        <strong>Representante:</strong> {formData.nombreCompleto}
                      </p>
                      <p>
                        <strong>Email:</strong> {formData.email}
                      </p>
                      <p>
                        <strong>Plan:</strong> {planes[formData.plan].nombre} -{' '}
                        {planes[formData.plan].precio}
                      </p>
                    </div>
                  </div>

                  <div className="space-y-4">
                    <label className="flex items-start">
                      <input
                        type="checkbox"
                        checked={formData.aceptaTerminos}
                        onChange={(e) =>
                          setFormData({ ...formData, aceptaTerminos: e.target.checked })
                        }
                        className="mt-1 mr-3"
                      />
                      <span className="text-sm">
                        Acepto los{' '}
                        <Link href="/terminos" className="text-primary-600 hover:underline">
                          términos y condiciones
                        </Link>{' '}
                        *
                      </span>
                    </label>
                    {errors.aceptaTerminos && (
                      <p className="text-sm text-red-600 ml-6">{errors.aceptaTerminos}</p>
                    )}

                    <label className="flex items-start">
                      <input
                        type="checkbox"
                        checked={formData.aceptaPrivacidad}
                        onChange={(e) =>
                          setFormData({ ...formData, aceptaPrivacidad: e.target.checked })
                        }
                        className="mt-1 mr-3"
                      />
                      <span className="text-sm">
                        Acepto la{' '}
                        <Link href="/privacidad" className="text-primary-600 hover:underline">
                          política de privacidad
                        </Link>{' '}
                        *
                      </span>
                    </label>
                    {errors.aceptaPrivacidad && (
                      <p className="text-sm text-red-600 ml-6">{errors.aceptaPrivacidad}</p>
                    )}
                  </div>
                </div>
              )}

              {/* Botones de navegación */}
              <div className="flex justify-between mt-8">
                {currentSection > 1 && (
                  <button
                    type="button"
                    onClick={prevSection}
                    className="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-semibold"
                  >
                    Anterior
                  </button>
                )}

                {currentSection < 5 ? (
                  <button
                    type="button"
                    onClick={nextSection}
                    className="ml-auto px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-semibold"
                  >
                    Siguiente
                  </button>
                ) : (
                  <button
                    type="submit"
                    disabled={loading}
                    className="ml-auto px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    {loading ? 'Creando cuenta...' : 'Crear cuenta'}
                  </button>
                )}
              </div>
            </form>

            <div className="mt-6 text-center">
              <Link href="/login" className="text-sm text-primary-600 hover:text-primary-700">
                ¿Ya tienes cuenta? Ingresar
              </Link>
            </div>
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
