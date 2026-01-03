import Link from 'next/link';

export default function Footer() {
  const currentYear = new Date().getFullYear();

  return (
    <footer className="bg-gray-900 text-gray-300">
      <div className="container mx-auto px-4 py-12">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          {/* Columna 1 - Marca */}
          <div className="space-y-4">
            <div className="flex items-center space-x-2">
              <div className="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
                <span className="text-white font-bold text-xl">C</span>
              </div>
              <span className="font-bold text-xl text-white">Conecta ERP</span>
            </div>
            <p className="text-sm text-gray-400">
              Plataforma ERP integral para empresas modernas.
            </p>
          </div>

          {/* Columna 2 - Navegación */}
          <div>
            <h3 className="font-semibold text-white mb-4">Navegación</h3>
            <ul className="space-y-2">
              <li>
                <Link href="/" className="text-sm hover:text-primary-400 transition-colors">
                  Inicio
                </Link>
              </li>
              <li>
                <Link href="/#planes" className="text-sm hover:text-primary-400 transition-colors">
                  Planes
                </Link>
              </li>
              <li>
                <Link href="/#funcionalidades" className="text-sm hover:text-primary-400 transition-colors">
                  Funcionalidades
                </Link>
              </li>
              <li>
                <Link href="/#contacto" className="text-sm hover:text-primary-400 transition-colors">
                  Contacto
                </Link>
              </li>
              <li>
                <Link href="/login" className="text-sm hover:text-primary-400 transition-colors">
                  Login
                </Link>
              </li>
              <li>
                <Link href="/register" className="text-sm hover:text-primary-400 transition-colors">
                  Crear cuenta
                </Link>
              </li>
            </ul>
          </div>

          {/* Columna 3 - Contacto */}
          <div>
            <h3 className="font-semibold text-white mb-4">Contacto</h3>
            <ul className="space-y-2 text-sm">
              <li className="flex items-center space-x-2">
                <span>📧</span>
                <a href="mailto:contacto@conectaerp.com" className="hover:text-primary-400 transition-colors">
                  contacto@conectaerp.com
                </a>
              </li>
              <li className="flex items-center space-x-2">
                <span>📞</span>
                <a href="tel:+56985745559" className="hover:text-primary-400 transition-colors">
                  +56 9 8574 5559
                </a>
              </li>
              <li className="text-gray-400">
                Horario de atención:<br />
                Lunes a Viernes 9:00 - 18:00
              </li>
            </ul>
          </div>

          {/* Columna 4 - Legal */}
          <div>
            <h3 className="font-semibold text-white mb-4">Legal</h3>
            <ul className="space-y-2">
              <li>
                <Link href="/terminos" className="text-sm hover:text-primary-400 transition-colors">
                  Términos y condiciones
                </Link>
              </li>
              <li>
                <Link href="/privacidad" className="text-sm hover:text-primary-400 transition-colors">
                  Política de privacidad
                </Link>
              </li>
              <li>
                <Link href="/aviso-legal" className="text-sm hover:text-primary-400 transition-colors">
                  Aviso legal
                </Link>
              </li>
            </ul>
          </div>
        </div>

        {/* Footer inferior */}
        <div className="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
          <p>© {currentYear} Conecta ERP - Todos los derechos reservados</p>
        </div>
      </div>
    </footer>
  );
}
