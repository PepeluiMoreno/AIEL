codigo = '00000000'
codigo_str = str(codigo).strip() if codigo else None
print(f"Original: '{codigo}'")
print(f"After strip: '{codigo_str}'")
print(f"Bool check: {bool(codigo_str)}")

# What we should do instead
codigo_normalized = str(codigo).strip().lstrip('0') if codigo else None
if codigo_normalized == '':
    codigo_normalized = '0'
print(f"After lstrip('0') with fallback: '{codigo_normalized}'")
