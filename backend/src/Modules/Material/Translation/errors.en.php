<?php

declare(strict_types=1);

return [
    'error.material_not_found'                   => 'Material not found.',
    'error.material_image_not_found'            => 'Material image not found.',
    'error.material_option_not_found'          => 'Material option not found.',
    'error.material_pricing_by_area_not_found' => 'Material pricing by area not found.',
    'error.material_pricing_by_piece_not_found' => 'Material pricing by piece not found.',
    'error.material_processing_not_found'      => 'Material processing not found.',
    'error.material_pricing_cut_not_found'     => 'Material pricing cut not found.',
    'error.material_option_by_area_cannot_have_piece_pricing' => 'Area-priced option cannot contain piece pricing rows.',
    'error.material_option_by_piece_cannot_have_area_pricing' => 'Piece-priced option cannot contain area pricing rows.',
    'error.material_option_without_cut_cannot_have_cut_pricing' => 'Cut pricing cannot be provided when cut is disabled for the option.',
    'error.material_pricing_by_area_duplicate' => 'Duplicate area pricing rows found.',
    'error.material_pricing_by_piece_duplicate' => 'Duplicate piece pricing rows found.',
    'error.material_pricing_cut_duplicate'     => 'Duplicate cut pricing rows found.',
    'error.material_processing_duplicate'      => 'Duplicate material-processing links found.',
];
