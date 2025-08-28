import { registerBlock } from "@/app/blocks/registry";

import UnknownBlock from "@/app/blocks/components/UnknownBlock";
import Paragraph from "@/app/blocks/components/Paragraph";
import Heading from "@/app/blocks/components/Heading";
import ImageBlock from "@/app/blocks/components/ImageBlock";
import VideoBlock from "@/app/blocks/components/VideoBlock";
import QuoteBlock from "@/app/blocks/components/QuoteBlock";
import Group from "@/app/blocks/components/Group";
import Columns from "@/app/blocks/components/Columns";
import Column from "@/app/blocks/components/Column";
import ListBlock from "@/app/blocks/components/ListBlock";

export function registerDefaultBlocks() {
  registerBlock("core/paragraph", Paragraph);
  registerBlock("core/heading", Heading);
  registerBlock("core/image", ImageBlock);
  registerBlock("core/video", VideoBlock);
  registerBlock("core/quote", QuoteBlock);
  registerBlock("core/group", Group);
  registerBlock("core/columns", Columns);
  registerBlock("core/column", Column);
  registerBlock("core/list", ListBlock);

  registerBlock("core/buttons", UnknownBlock);
  registerBlock("core/button", UnknownBlock);
}
