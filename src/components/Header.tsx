'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';

export default function Header() {
  const [isScrolled, setIsScrolled] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 10);
    };

    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  return (
    <header
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
        isScrolled
          ? 'bg-white shadow-md'
          : 'bg-transparent'
      }`}
    >
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between h-16">
          {/* Logo */}
          <Link href="/" className="flex items-center space-x-2">
            <div className="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
              <span className="text-white font-bold text-xl">C</span>
            </div>
            <span className={`font-bold text-xl ${isScrolled ? 'text-gray-900' : 'text-gray-900'}`}>
              Conecta ERP
            </span>
          </Link>

          {/* Navigation Menu */}
          <nav className="hidden md:flex items-center space-x-8">
            <Link
              href="/"
              className={`transition-colors ${
                isScrolled ? 'text-gray-700 hover:text-primary-600' : 'text-gray-900 hover:text-primary-600'
              }`}
            >
              Inicio
            </Link>
            <Link
              href="/#planes"
              className={`transition-colors ${
                isScrolled ? 'text-gray-700 hover:text-primary-600' : 'text-gray-900 hover:text-primary-600'
              }`}
            >
              Planes
            </Link>
            <Link
              href="/#funcionalidades"
              className={`transition-colors ${
                isScrolled ? 'text-gray-700 hover:text-primary-600' : 'text-gray-900 hover:text-primary-600'
              }`}
            >
              Funcionalidades
            </Link>
            <Link
              href="/#contacto"
              className={`transition-colors ${
                isScrolled ? 'text-gray-700 hover:text-primary-600' : 'text-gray-900 hover:text-primary-600'
              }`}
            >
              Contacto
            </Link>
          </nav>

          {/* Action Buttons */}
          <div className="flex items-center space-x-4">
            <Link
              href="/login"
              className={`px-4 py-2 rounded-lg transition-colors ${
                isScrolled
                  ? 'text-gray-700 hover:text-primary-600'
                  : 'text-gray-900 hover:text-primary-600'
              }`}
            >
              Ingresar
            </Link>
            <Link
              href="/register"
              className="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors shadow-md"
            >
              Crear cuenta
            </Link>
          </div>
        </div>
      </div>
    </header>
  );
}
