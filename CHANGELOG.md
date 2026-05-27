# Changelog

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html)
and [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) (Princípio V — Constituição).

## [Unreleased]

### Added
- `Providers/Nacional`: Provedor REST/JSON para o Padrão Nacional NFS-e (ADN)
  — classes `Nacional`, `NacionalClient`, `NacionalTransformer`, `ConfiguracaoNacional`
- Hierarquia de exceções tipadas: `NacionalException`, `ValidationException`,
  `AuthException`, `NotFoundException`, `AdnException`, `TimeoutException`
- Value objects DPS: `Dps`, `Emitente`, `Tomador`, `Servico`, `Valores` e demais modelos
- Objetos de resposta: `RespostaEmissao`, `RespostaConsulta`, `RespostaCancelamento`
- Integração na Factory `NFSeStatic` para reconhecer `cmun = '0000000'` ou flag `padraoNacional`
- Dependência `guzzlehttp/guzzle ^7.0` para transporte HTTP com mTLS

### Changed
- Requisito mínimo de PHP atualizado para `^8.1` (conforme Constituição — Technology Constraints)
- `require-dev`: PHPUnit atualizado para `^10.0`; adicionados `phpstan/phpstan ^1.0`
  e `friendsofphp/php-cs-fixer ^3.0`

---

## 4.1.0-dev 

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing
