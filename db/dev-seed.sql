SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


INSERT INTO `role` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'user');

INSERT INTO `user` (`id`, `role_id`, `name`, `email`, `password`) VALUES
(1, 1, 'admin', 'admin@email.com', '$2y$13$piJjfbFK1vHD508xeirlU.cmpPf8TMjLk.zm2VJmbfxYSnfm9.hJ2'),
(2, 2, 'user', 'user@email.com', '$2y$13$nYqiTj5R2UQZZDHs1JFF/e53n9LfNG1NLfr/Ji8IORcpy9z0UNdUe');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
