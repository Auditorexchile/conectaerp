/**
 * Validador de RUT Chileno
 * Valida formato y dígito verificador
 */

export const formatRut = (rut: string): string => {
  // Eliminar puntos y guiones
  const cleanRut = rut.replace(/\./g, '').replace(/-/g, '');

  // Separar número y dígito verificador
  const rutNum = cleanRut.slice(0, -1);
  const dv = cleanRut.slice(-1).toUpperCase();

  // Formatear con puntos y guion
  const formattedNum = rutNum.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

  return `${formattedNum}-${dv}`;
};

export const cleanRut = (rut: string): string => {
  return rut.replace(/\./g, '').replace(/-/g, '');
};

export const calculateDV = (rut: string): string => {
  const cleanedRut = rut.replace(/\./g, '').replace(/-/g, '').slice(0, -1);

  let sum = 0;
  let multiplier = 2;

  // Sumar desde el final
  for (let i = cleanedRut.length - 1; i >= 0; i--) {
    sum += parseInt(cleanedRut[i]) * multiplier;
    multiplier = multiplier === 7 ? 2 : multiplier + 1;
  }

  const remainder = sum % 11;
  const dv = 11 - remainder;

  if (dv === 11) return '0';
  if (dv === 10) return 'K';
  return dv.toString();
};

export const validateRut = (rut: string): boolean => {
  if (!rut || rut.trim() === '') return false;

  const cleanedRut = cleanRut(rut);

  // Validar longitud mínima
  if (cleanedRut.length < 2) return false;

  // Obtener el dígito verificador ingresado
  const inputDV = cleanedRut.slice(-1).toUpperCase();

  // Calcular el dígito verificador correcto
  const calculatedDV = calculateDV(cleanedRut);

  return inputDV === calculatedDV;
};

export const isValidRutFormat = (rut: string): boolean => {
  // Validar formato: XX.XXX.XXX-X o XXXXXXXX-X
  const rutPattern = /^(\d{1,2}\.?\d{3}\.?\d{3}-[\dkK])$/;
  return rutPattern.test(rut.replace(/\./g, ''));
};
