# AGENTS.md - import-product-variant

## Zweck & Verantwortung

Das `import-product-variant` Modul bietet **Configurable Product (Variant) Import-Funktionalität** für komplexe Produktvariationen. Es ist ein **Tier 5 Modul** in der Import-Architektur und erweitert das `import-product` Modul mit spezialisierten Funktionen für konfigurierbare Produkte.

**Hauptverantwortung:**
- Configurable Product Import und Initialisierung
- Super Attribute Import (definiert konfigurierbare Attribute)
- Variant Association Import (verknüpft Simple mit Configurable)
- Repository Pattern Implementation für persistente Speicherung
- Service Layer für Configurable-spezifische Business Logic
- Observer Pattern für Hook-Integration in der Import-Pipeline

**Modul-Kategorie:** Integration/Extension Module  
**Komplexität:** ⭐⭐⭐⭐ (Hoch - komplexe Attribut-Struktur)

## Architektur & Design Patterns

### Kern-Klassen
- **ConfigurableProductRepository**: Persistiert Configurable Product Metadaten
- **SuperAttributeRepository**: Verwaltet Super Attributes (konfigurierbare Attribute)
- **VariantAssociationRepository**: Verknüpft Simple Produkte als Varianten
- **ConfigurableProductProcessor**: Service Layer für Configurable-Verarbeitung
- **ConfigurableProductObserver**: Observer für Lifecycle Hooks
- **AttributeAssociationManager**: Koordiniert Attribut-zu-Produkt Zuordnung

### Verwendete Patterns
- **Observer Pattern**: Zur Einklinken in Import-Lifecycle Events
- **Repository Pattern**: Für abstrakte Datenschicht
- **Service Layer Pattern**: Geschäftslogik isoliert von Repositories
- **Factory Pattern**: Für Object-Erstellung und Instantiation
- **Strategy Pattern**: Verschiedene Attribute-Handling-Strategien

### Datenfluss
```
Configurable CSV
    ↓
Parser (import-serializer)
    ↓
Converter (import-converter)
    ↓
Configurable Product Processor
    ├─→ ConfigurableProductRepository (Basis)
    ├─→ SuperAttributeRepository (Attribute)
    └─→ VariantAssociationRepository (Simple-Links)
    ↓
Magento Database (catalog_product_super_*)
```

## Abhängigkeiten

### Externe Pakete
- **Keine direkten PHP-Pakete**

### TechDivision Dependencies
- **import-product** ^26.2 - Base Product Importer (Parent)
- **import-converter** - Data Conversion Framework
- **import-attribute** - Attribute Import Framework

### Abhängig von diesem Modul (1 Reverse Dependency)
- **import-product-variant-ee** - EE-spezifische Configurable Extensions

## Wichtige Entry Points

### Repository Klassen
```php
// Configurable Product Repository - Hauptklasse für Configurable
ConfigurableProductRepository::create($row): void
ConfigurableProductRepository::findByProductId($productId): ConfigurableProduct

// Super Attribute Repository - für konfigurierbare Attribute
SuperAttributeRepository::create($row): void
SuperAttributeRepository::findByProductId($productId): array

// Variant Association Repository - Simple zu Configurable Verknüpfungen
VariantAssociationRepository::create($row): void
VariantAssociationRepository::findByParentProductId($parentId): array
```

### Service Methods
- `ConfigurableProductProcessor::process()` - Haupteingangspunkt
- `ConfigurableProductProcessor::validateAttributes()` - Validierung Attribute
- `ConfigurableProductProcessor::associateVariants()` - Simple Products verknüpfen

## Events & Extension Points

**Keine Custom Events** - Tier 5 Importer-Modul nutzt Parent-Events aus import-product

### Observer Hooks
- `product.import.variant.validate.pre` - Vor Validierung
- `product.import.variant.attribute.process.post` - Nach Super Attribute Verarbeitung
- `product.import.variant.association.process.post` - Nach Variant Association
- `product.import.variant.persist.error` - Bei Fehler

## Database Schema

### Relevante Tabellen
- **catalog_product_super_attribute** - Super Attributes für Configurable
  - `product_id` (Configurable Parent)
  - `attribute_id` (Konfigurierbare Attribute)
  - `position` - Reihenfolge in Frontend

- **catalog_product_super_attribute_label** - Attribute Labels
  - `product_super_attribute_id`
  - `store_id`
  - `use_default`, `value` (Label Text)

- **catalog_product_super_link** - Simple zu Configurable Verknüpfung
  - `product_id` (Simple Variant)
  - `parent_id` (Configurable Parent)

- **catalog_product_relation** - Cross-Link für Relationen
  - `parent_id` (Configurable)
  - `child_id` (Simple Variant)

## Common Use Cases

### Use Case 1: Configurable Products mit Größen/Farben
```php
// CSV Dateistruktur:
// sku,attribute_set,super_attribute_color,super_attribute_size,variant_sku_1_color_1_size_1

// SHIRT-PARENT,shirts,color|size,size,SHIRT-001-RED-M
// Verarbeitung:
// 1. Erstellt Configurable Product (SHIRT-PARENT)
// 2. Registriert color und size als Super Attributes
// 3. Verknüpft Simple Product (SHIRT-001-RED-M) als Variant
```

### Use Case 2: Custom Attribute Handling
```php
class CustomVariantProcessor {
    public function processVariants($configurableData) {
        // Hook nach Variant-Verarbeitung
        $this->eventManager->dispatch('custom.variant.process', [
            'configurable_product' => $configurableData,
            'super_attributes' => $configurableData['super_attributes']
        ]);
    }
}
```

## Performance Considerations

### Wichtige Performance-Aspekte
1. **Attribut-Lookups**: Super Attribute werden mehrfach nachgeschlagen
2. **Variant-Zuordnung**: Jede Simple Product wird mit Parent gelinkt
3. **Label-Inserts**: Für jedes Attribut und Store ein Label-Record
4. **Relation Lookups**: Product IDs werden für Links gesucht

### Optimierungen
- Cache Attribute-IDs während Import
- Batch Insert für Labels (max 1000 pro Batch)
- Pre-load alle Simple Product-IDs
- Nutze Database Transactions für Consistency

## Hints für KI-Agenten

### Kritisches Verständnis
1. **Tier 5 Modul**: Spezialisierte Extension des Product Importers
2. **Configurable-fokussiert**: AUSSCHLIESSLICH für Configurable Product Type
3. **Attribut-basiert**: Super Attributes definieren Variationen
4. **Observer Pattern**: Integration mit Import-Pipeline durch Hooks
5. **Multi-Table**: Arbeitet mit 4 Datenbank-Tabellen

### Häufige Fehler
- ❌ Attribut-IDs falsch verwenden
- ❌ Variant-Product-IDs nicht validieren
- ❌ Super Attribute-Reihenfolge ignorieren
- ❌ Labels nicht für alle Stores erstellen
- ❌ Relation nicht bidirektional erstellen

### Best Practices
- ✅ Validiere dass Super Attributes existieren
- ✅ Validiere dass alle Simple Products existieren
- ✅ Nutze Repository-Pattern für alle Datenzugriffe
- ✅ Erstelle Labels für alle Stores
- ✅ Teste mit echten mehrstufigen CSV-Dateien

## Known Limitations

- **Product-Type spezifisch**: Funktioniert nur mit Configurable Products (type_id = configurable)
- **Attribute-Abhängig**: Erfordert dass Super Attributes bereits existieren
- **Simple-Abhängig**: Alle Varianten müssen als Simple Products existieren
- **Keine Multi-Select**: Super Attributes können nur eindeutig sein
- **Label-Overhead**: Muss Labels für jeden Store erstellen

## Related Modules

### Direct Dependencies
- **import-product** - Base Product Importer (Parent)
- **import-attribute** - Attribute Import Framework

### Related/Companion Modules
- **import-product-variant-ee** - EE-spezifische Configurable Extensions
- **import-product-bundle** - Bundle Product Importer (Alternative Grouping)
- **import-product-grouped** - Grouped Product Importer

## Troubleshooting

### Problem: Configurable Products nicht importiert
**Mögliche Ursachen:**
1. Parent Product existiert nicht
2. Super Attributes sind nicht registriert
3. Attribute-IDs sind falsch

**Lösung:**
- Validiere dass Parent Product im System ist
- Prüfe dass Super Attributes bereits existieren
- Validiere dass attribute_id korrekt ist

### Problem: Varianten werden nicht verknüpft
**Mögliche Ursachen:**
1. Simple Products existieren nicht
2. Product IDs falsch
3. CSV-Format nicht korrekt

**Lösung:**
- Validiere dass alle Simple Products bereits im System sind
- Prüfe dass variant_sku korrekt ist
- Verwende korrektes CSV-Format

### Problem: Attribute Labels erscheinen nicht
**Mögliche Ursachen:**
1. Labels nicht für alle Stores erstellt
2. Store-IDs nicht korrekt

**Lösung:**
- Stelle sicher dass Labels für ALLE Stores erstellt werden
- Validiere dass store_id korrekt ist

## Zusammenfassung

`import-product-variant` ist ein **Tier 5 Importer-Modul**, das spezialisierte Configurable Product Import-Funktionalität mit komplexer Attribut- und Variant-Struktur bereitstellt. Es verwaltet die Verknüpfung von Simple Products als Varianten zu Configurable Parents mit Super Attributes.

**Für KI-Agenten:** Verstehe dieses Modul als:
- **Configurable Product Importer** mit Attribut- und Variant-Verwaltung
- **Tier 5 Integration** in die generische Import-Pipeline
- **Attribut-fokussiert** mit Super Attribute Management
- **Multi-Table Handling** mit Relation und Label Management
