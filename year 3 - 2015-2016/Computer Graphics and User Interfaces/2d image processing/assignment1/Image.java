package assignment1;

import javax.imageio.ImageIO;
import java.awt.image.BufferedImage;
import java.io.File;
import java.io.IOException;

public class Image {

    private String path;
    private BufferedImage original;
    private BufferedImage filtered;

    public Image(String path) {
        this.path = path;
        open();
    }

    /**
     * Read the image into a BufferedImage
     */
    protected void open() {
        File file = new File(path);

        try {
            original = filtered = ImageIO.read(file);
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    /**
     * Apply the filter to the image
     *
     * @param filter
     */
    public void apply(IFilter filter) {
        filtered = filter.apply(filtered);
    }

    /**
     * Get the unmodified image
     *
     * @return BufferedImage
     */
    public BufferedImage getOriginal() {
        return original;
    }

    /**
     * Get the image after filter were applied to the image
     *
     * @return BufferedImage
     */
    public BufferedImage getFiltered() {
        return filtered;
    }
}
