import Header from '@/components/Header';
import Footer from '@/components/Footer';
import Link from 'next/link';

export default function Home() {
  return (
    <div className="min-h-screen">
      <Header />

      {/* HERO PRINCIPAL */}
      <section className="pt-32 pb-20 px-4 bg-gradient-to-br from-blue-50 to-indigo-100">
        <div className="container mx-auto">
          <div className="grid md:grid-cols-2 gap-12 items-center">
            <div className="space-y-6">
              <h1 className="text-5xl md:text-6xl font-bold text-gray-900 leading-tight">
                Gestiona tu empresa en un solo sistema
              </h1>
              <p className="text-xl text-gray-600">
                Contabilidad, ventas, inventario, producción y control total en un ERP moderno.
              </p>
              <div className="flex flex-col sm:flex-row gap-4">
                <Link
                  href="/register"
                  className="px-8 py-4 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors shadow-lg text-center font-semibold"
                >
                  Crear cuenta
                </Link>
                <Link
                  href="#planes"
                  className="px-8 py-4 bg-white text-primary-600 rounded-lg hover:bg-gray-50 transition-colors shadow-lg text-center font-semibold border-2 border-primary-600"
                >
                  Ver planes
                </Link>
              </div>
            </div>
            <div className="hidden md:block">
              <div className="bg-white rounded-2xl shadow-2xl p-8">
                <div className="space-y-4">
                  <div className="h-8 bg-gradient-to-r from-primary-400 to-primary-600 rounded w-3/4"></div>
                  <div className="h-32 bg-gradient-to-br from-blue-100 to-indigo-200 rounded"></div>
                  <div className="grid grid-cols-3 gap-4">
                    <div className="h-20 bg-green-100 rounded"></div>
                    <div className="h-20 bg-yellow-100 rounded"></div>
                    <div className="h-20 bg-red-100 rounded"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* BENEFICIOS */}
      <section id="funcionalidades" className="py-20 px-4 bg-white">
        <div className="container mx-auto">
          <div className="text-center mb-16">
            <h2 className="text-4xl font-bold text-gray-900 mb-4">
              ¿Por qué elegir Conecta ERP?
            </h2>
            <p className="text-xl text-gray-600">
              La solución completa para tu empresa
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            {/* Card 1 */}
            <div className="bg-white border-2 border-gray-200 rounded-xl p-6 hover:border-primary-500 hover:shadow-lg transition-all">
              <div className="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-4">
                <span className="text-2xl">🔗</span>
              </div>
              <h3 className="text-xl font-bold text-gray-900 mb-2">
                Todo integrado
              </h3>
              <p className="text-gray-600">
                Un solo sistema para todas las operaciones de tu empresa
              </p>
            </div>

            {/* Card 2 */}
            <div className="bg-white border-2 border-gray-200 rounded-xl p-6 hover:border-primary-500 hover:shadow-lg transition-all">
              <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                <span className="text-2xl">📋</span>
              </div>
              <h3 className="text-xl font-bold text-gray-900 mb-2">
                Cumplimiento tributario
              </h3>
              <p className="text-gray-600">
                Preparado para SII y normativas chilenas
              </p>
            </div>

            {/* Card 3 */}
            <div className="bg-white border-2 border-gray-200 rounded-xl p-6 hover:border-primary-500 hover:shadow-lg transition-all">
              <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                <span className="text-2xl">📈</span>
              </div>
              <h3 className="text-xl font-bold text-gray-900 mb-2">
                Escalable
              </h3>
              <p className="text-gray-600">
                Crece con tu empresa sin límites
              </p>
            </div>

            {/* Card 4 */}
            <div className="bg-white border-2 border-gray-200 rounded-xl p-6 hover:border-primary-500 hover:shadow-lg transition-all">
              <div className="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                <span className="text-2xl">🔒</span>
              </div>
              <h3 className="text-xl font-bold text-gray-900 mb-2">
                Seguro
              </h3>
              <p className="text-gray-600">
                Acceso protegido y auditoría completa
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* PLANES */}
      <section id="planes" className="py-20 px-4 bg-gray-50">
        <div className="container mx-auto">
          <div className="text-center mb-16">
            <h2 className="text-4xl font-bold text-gray-900 mb-4">
              Planes para cada tipo de empresa
            </h2>
            <p className="text-xl text-gray-600">
              Elige el plan que mejor se adapte a tus necesidades
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            {/* PLAN 1 - STARTER */}
            <div className="bg-white rounded-xl shadow-lg p-8 border-2 border-gray-200 hover:border-starter hover:shadow-xl transition-all">
              <div className="text-center mb-6">
                <div className="inline-block px-4 py-1 bg-starter text-white rounded-full text-sm font-semibold mb-4">
                  STARTER
                </div>
                <div className="text-4xl font-bold text-gray-900 mb-2">
                  Gratis
                </div>
                <p className="text-gray-600">Comienza ahora</p>
              </div>
              <ul className="space-y-3 mb-8">
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">1 empresa</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">1 usuario</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Productos ilimitados</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Facturación básica</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Inventario básico</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Soporte email</span>
                </li>
              </ul>
              <Link
                href="/register?plan=starter"
                className="block w-full py-3 bg-starter text-white rounded-lg hover:bg-yellow-600 transition-colors text-center font-semibold"
              >
                Comenzar
              </Link>
            </div>

            {/* PLAN 2 - PROFESIONAL */}
            <div className="bg-white rounded-xl shadow-lg p-8 border-2 border-profesional hover:shadow-xl transition-all transform lg:scale-105">
              <div className="text-center mb-6">
                <div className="inline-block px-4 py-1 bg-profesional text-white rounded-full text-sm font-semibold mb-4">
                  PROFESIONAL
                </div>
                <div className="text-4xl font-bold text-gray-900 mb-2">
                  $49.990
                  <span className="text-lg text-gray-600">/mes</span>
                </div>
                <p className="text-gray-600">Más popular</p>
              </div>
              <ul className="space-y-3 mb-8">
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">1 empresa</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Hasta 5 usuarios</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Contabilidad completa</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Inventario avanzado</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Compras y ventas</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Reportes</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Soporte email + chat</span>
                </li>
              </ul>
              <Link
                href="/register?plan=profesional"
                className="block w-full py-3 bg-profesional text-white rounded-lg hover:bg-green-600 transition-colors text-center font-semibold"
              >
                Elegir plan
              </Link>
            </div>

            {/* PLAN 3 - EMPRESA */}
            <div className="bg-white rounded-xl shadow-lg p-8 border-2 border-gray-200 hover:border-empresa hover:shadow-xl transition-all">
              <div className="text-center mb-6">
                <div className="inline-block px-4 py-1 bg-empresa text-white rounded-full text-sm font-semibold mb-4">
                  EMPRESA
                </div>
                <div className="text-4xl font-bold text-gray-900 mb-2">
                  $99.990
                  <span className="text-lg text-gray-600">/mes</span>
                </div>
                <p className="text-gray-600">Para empresas</p>
              </div>
              <ul className="space-y-3 mb-8">
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">1 empresa</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Usuarios ilimitados</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Producción</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Costos</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Proyectos</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Multi moneda</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Integración SII</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Soporte prioritario</span>
                </li>
              </ul>
              <Link
                href="/register?plan=empresa"
                className="block w-full py-3 bg-empresa text-white rounded-lg hover:bg-blue-700 transition-colors text-center font-semibold"
              >
                Elegir plan
              </Link>
            </div>

            {/* PLAN 4 - CORPORATIVO */}
            <div className="bg-white rounded-xl shadow-lg p-8 border-2 border-gray-200 hover:border-corporativo hover:shadow-xl transition-all">
              <div className="text-center mb-6">
                <div className="inline-block px-4 py-1 bg-corporativo text-white rounded-full text-sm font-semibold mb-4">
                  CORPORATIVO
                </div>
                <div className="text-4xl font-bold text-gray-900 mb-2">
                  Contactar
                </div>
                <p className="text-gray-600">A medida</p>
              </div>
              <ul className="space-y-3 mb-8">
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Multiempresa</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Multi sucursal</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Control gestión</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">BI</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Integraciones</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">SLA dedicado</span>
                </li>
                <li className="flex items-start">
                  <span className="text-green-500 mr-2">✓</span>
                  <span className="text-sm">Soporte premium</span>
                </li>
              </ul>
              <Link
                href="#contacto"
                className="block w-full py-3 bg-corporativo text-white rounded-lg hover:bg-red-600 transition-colors text-center font-semibold"
              >
                Contactar
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* CTA FINAL */}
      <section id="contacto" className="py-20 px-4 bg-gradient-to-r from-primary-600 to-indigo-600">
        <div className="container mx-auto text-center">
          <h2 className="text-4xl md:text-5xl font-bold text-white mb-6">
            Comienza hoy y controla tu empresa desde un solo lugar
          </h2>
          <Link
            href="/register"
            className="inline-block px-10 py-4 bg-white text-primary-600 rounded-lg hover:bg-gray-100 transition-colors shadow-xl text-lg font-semibold"
          >
            Crear cuenta gratis
          </Link>
        </div>
      </section>

      <Footer />
    </div>
  );
}
