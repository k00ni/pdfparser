<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey;

enum DictionaryKey: string
{
    case AA                 = 'AA';
    case ACRO_FORM          = 'AcroForm';
    case ASCENT             = 'Ascent';
    case AUTH_EVENT         = 'AuthEvent';
    case BASE_FONT          = 'BaseFont';
    case CAP_HEIGHT         = 'CapHeight';
    case CF                 = 'CF';
    case CFM                = 'CFM';
    case CHAR_SET           = 'CharSet';
    case COLUMNS            = 'Columns';
    case COLLECTION         = 'Collection';
    case CONTENTS           = 'Contents';
    case COUNT              = 'Count';
    case CREATOR            = 'Creator';
    case CREATION_DATE      = 'CreationDate';
    case DECODE_PARAMS      = 'DecodeParms';
    case DESCENT            = 'Descent';
    case DESTS              = 'Dests';
    case EFF                = 'EFF';
    case ENCRYPT_METADATA   = 'EncryptMetadata';
    case ENCRYPTION         = 'Encrypt';
    case EXTENDS            = 'Extends';
    case EXTENSIONS         = 'Extensions';
    case F                  = 'F';
    case FILTER             = 'Filter';
    case FIRST              = 'First';
    case FIRST_CHAR         = 'FirstChar';
    case FLAGS              = 'Flags';
    case FONT               = 'Font';
    case FONT_B_BOX         = 'FontBBox';
    case FONT_DESCRIPTOR    = 'FontDescriptor';
    case FONT_FILE          = 'FontFile';
    case FONT_NAME          = 'FontName';
    case ID                 = 'ID';
    case INDEX              = 'Index';
    case INFO               = 'Info';
    case ITALIC_ANGLE       = 'ItalicAngle';
    case KIDS               = 'Kids';
    case LANG               = 'Lang';
    case LAST_CHAR          = 'LastChar';
    case LEGAL              = 'Legal';
    case LENGTH             = 'Length';
    case MEDIABOX           = 'MediaBox';
    case MARK_INFO          = 'MarkInfo';
    case METADATA           = 'Metadata';
    case MOD_DATE           = 'ModDate';
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
    case PDF                = 'PDF';
    case PERMS              = 'Perms';
    case PIECE_INFO         = 'PieceInfo';
    case PREDICTOR          = 'Predictor';
    case PREVIOUS           = 'Prev';
    case PROCSET            = 'ProcSet';
    case PRODUCER           = 'Producer';
    case PTEX_FULL_BANNER   = 'PTEX.Fullbanner';
    case R                  = 'R';
    case RECIPIENTS         = 'Recipients';
    case REQUIREMENTS       = 'Requirements';
    case RESOURCES          = 'Resources';
    case ROOT               = 'Root';
    case SIZE               = 'Size';
    case SPIDER_INFO        = 'SpiderInfo';
    case STEM_V             = 'StemV';
    case STMF               = 'StmF';
    case STRF               = 'StrF';
    case STRUCT_TREE_ROOT   = 'StructTreeRoot';
    case SUB_FILTER         = 'SubFilter';
    case SUBTYPE            = 'Subtype';
    case THREADS            = 'Threads';
    case TRAPPED            = 'Trapped';
    case TYPE               = 'Type';
    case U                  = 'U';
    case URI                = 'URI';
    case V                  = 'V';
    case VERSION            = 'Version';
    case VIEWER_PREFERENCES = 'ViewerPreferences';
    case W                  = 'W';
    case WIDTHS             = 'Widths';
    case X_HEIGHT           = 'XHeight';
    case XREFSTM            = 'XRefStm';

    public static function fromKeyString(string $keyString): self
    {
        return self::from(ltrim($keyString, '/'));
    }
}
