# AGENTS.md - import-product-variant

## Zweck & Verantwortung

Das `import-product-variant` Modul bietet **Configurable Product (Variant) Import-Funktionalität**. Es ist ein **Tier 5 Modul** und erweitert `import-product`.

**Hauptverantwortung:**
- Configurable Product Import
- Super Attribute Import
- Variant Association Import
- Repository Pattern für Configurable Products
- Service Layer für Variant-Verarbeitung
- Observer Pattern für Variant-Hooks

## Architektur & Design Patterns

### Kern-Klassen
- **ConfigurableProductRepository**: Persistierung von Configurable Products
- **SuperAttributeRepository**: Persistierung von Super Attributes
- **VariantAssociationRepository**: Persistierung von Variant Associations
- **VariantObserver**: Observer für Hooks

### Verwendete Patterns
- **Observer Pattern**: Für Variant-Hooks
- **Repository Pattern**: Für Daten-Persistierung
- **Service Layer**: Für Business Logic

## Abhängigkeiten

### Externe Pakete
- **Keine**

### TechDivision Dependencies
- **import-product** ^26.2 - Product Importer

### Abhängig von diesem Modul (1 Reverse Dependency)
- **import-product-variant-ee** - EE Variant Extensions

## Wichtige Entry Points

### Repository Klassen
```php
// Configurable Product Repository
ConfigurableProductRepository::create($row): void

// Super Attribute Repository
SuperAttributeRepository::create($row): void

// Variant Association Repository
VariantAssociationRepository::create($row): void
```

## Events & Extension Points

**Keine Events** - Tier 5 Importer-Modul

## Hints für KI-Agenten

### Wichtig zu verstehen
1. **Tier 5 Modul**: Erweitert Product Importer
2. **Variant-fokussiert**: Spezialisiert auf Configurable Products
3. **Observer Pattern**: Für Hooks
4. **Repository Pattern**: Für Persistierung

## Bekannte Einschränkungen

- **Variant-Only**: Keine anderen Product-Typen
- **Abhängig von Products**: Erfordert Products zu existieren

## Zusammenfassung

`import-product-variant` ist ein **Tier 5 Modul**, das Configurable Product Import-Funktionalität bietet. Es erweitert den Product Importer mit spezialisierter Funktionalität für Configurable Products und Variants.

**Für Agenten:** Verstehe dieses Modul als **Configurable Product Importer** mit Observer und Repository Pattern.
