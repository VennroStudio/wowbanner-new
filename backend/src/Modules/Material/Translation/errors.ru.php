<?php

declare(strict_types=1);

return [
    'error.material_not_found'                   => 'Материал не найден.',
    'error.material_image_not_found'            => 'Изображение материала не найдено.',
    'error.material_option_not_found'          => 'Вариант материала не найден.',
    'error.material_pricing_by_area_not_found' => 'Ценообразование материала по площади не найдено.',
    'error.material_pricing_by_piece_not_found' => 'Ценообразование материала поштучно не найдено.',
    'error.material_processing_not_found'      => 'Связь материала с обработкой не найдена.',
    'error.material_pricing_cut_not_found'     => 'Ценообразование реза материала не найдено.',
    'error.material_option_by_area_cannot_have_piece_pricing' => 'Вариант с ценообразованием по площади не может содержать поштучные цены.',
    'error.material_option_by_piece_cannot_have_area_pricing' => 'Вариант с поштучным ценообразованием не может содержать цены по площади.',
    'error.material_option_without_cut_cannot_have_cut_pricing' => 'Нельзя передавать цены на рез, если рез для варианта отключен.',
    'error.material_pricing_by_area_duplicate' => 'Найдены дублирующиеся строки ценообразования по площади.',
    'error.material_pricing_by_piece_duplicate' => 'Найдены дублирующиеся строки поштучного ценообразования.',
    'error.material_pricing_cut_duplicate'     => 'Найдены дублирующиеся строки ценообразования реза.',
    'error.material_processing_duplicate'      => 'Найдены дублирующиеся связи материала с обработкой.',
];
