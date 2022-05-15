<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey;

enum DictionaryKey: string
{
    case AA                 = 'AA';
    case ACRO_FORM          = 'AcroForm';
    case AUTH_EVENT         = 'AuthEvent';
    case CF                 = 'CF';
    case CFM                = 'CFM';
    case COLLECTION         = 'Collection';
    case COUNT              = 'Count';
    case DESTS              = 'Dests';
    case EFF                = 'EFF';
    case ENCRYPT_METADATA   = 'EncryptMetadata';
    case ENCRYPTION         = 'Encrypt';
    case EXTENDS            = 'Extends';
    case EXTENSIONS         = 'Extensions';
    case FILTER             = 'Filter';
    case FIRST              = 'First';
    case ID                 = 'ID';
    case INDEX              = 'Index';
    case INFO               = 'Info';
    case KIDS               = 'Kids';
    case LANG               = 'Lang';
    case LEGAL              = 'Legal';
    case LENGTH             = 'Length';
    case MARK_INFO          = 'MarkInfo';
    case METADATA           = 'Metadata';
    case N                  = 'N';
    case NAMES              = 'Names';
    case NEEDS_RENDERING    = 'NeedsRendering';
    case O                  = 'O';
    case OC_PROPERTIES      = 'OCProperties';
    case OPEN_ACTION        = 'OpenAction';
    case OUTLINES           = 'Outlines';
    case OUTPUT_INTENTS     = 'OutputIntents';
    case P                  = 'P';
    case PAGE_LABELS        = 'PagesLabels';
    case PAGE_LAYOUT        = 'PageLayout';
    case PAGE_MODE          = 'PageMode';
    case PAGES              = 'Pages';
    case PARENT             = 'Parent';
    case PERMS              = 'Perms';
    case PIECE_INFO         = 'PieceInfo';
    case PREVIOUS           = 'Prev';
    case R                  = 'R';
    case RECIPIENTS         = 'Recipients';
    case REQUIREMENTS       = 'Requirements';
    case ROOT               = 'Root';
    case SIZE               = 'Size';
    case SPIDER_INFO        = 'SpiderInfo';
    case STMF               = 'StmF';
    case STRF               = 'StrF';
    case STRUCT_TREE_ROOT   = 'StructTreeRoot';
    case SUB_FILTER         = 'SubFilter';
    case THREADS            = 'Threads';
    case TYPE               = 'Type';
    case U                  = 'U';
    case URI                = 'URI';
    case V                  = 'V';
    case VERSION            = 'Version';
    case VIEWER_PREFERENCES = 'ViewerPreferences';
    case W                  = 'W';
    case XREFSTM            = 'XRefStm';

    public static function tryFromKeyString(string $keyString): ?static
    {
        return self::tryFrom(ltrim($keyString, '/'));
    }
}
