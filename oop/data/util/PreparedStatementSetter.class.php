<?php

class PreparedStatementSetter
{
    public static function setValuesAndExecute( $setValues, SQLite3Stmt &$stmt )
    {
        $setValues( $stmt );

        $result = null;
        try {
            $result = $stmt->execute();
        } catch ( Exception $e )
        {
            printf( 'Fail Whale: %s', $e->getMessage() );
        }

        return $result;
    }
}

?>