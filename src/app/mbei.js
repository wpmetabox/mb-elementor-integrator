import Mbgroupfield from './controls/mbGroupField';
import MbSelect2 from './controls/mbSelect2';

var MBEIControls = ( ( controls ) => {
    // console.log( "WIDGET: ", elementor );
    elementor.addControlView( 'mb_group_field', controls.BaseData.extend( Mbgroupfield( ) ) );
    // elementor.addControlView( 'repeater', controls.Repeater.extend( MbSelect2( ) ) );
} );
window.addEventListener( 'elementor/init', ( ) => MBEIControls( elementor.modules.controls ) )