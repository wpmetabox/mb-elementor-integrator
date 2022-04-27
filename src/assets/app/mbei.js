import Mbgroupfield from './controls/mbGroupField';
import MbSelect from './controls/mbSelect';

var MBEIControls = ( ( controls ) => {
    // console.log( "WIDGET: ", elementor );
    elementor.addControlView( 'mb_group_field', controls.BaseData.extend( Mbgroupfield( ) ) );
    elementor.addControlView( 'select', controls.Select.extend( MbSelect( ) ) );
} );
window.addEventListener( 'elementor/init', ( ) => MBEIControls( elementor.modules.controls ) )